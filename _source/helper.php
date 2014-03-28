<?php
/**
 * Helper class for Stack
 *
 * @package    jevolve.extensions
 * @subpackage Modules
 * @link       http://jevolve.net
 * @license    GNU/GPL, see LICENSE.php
 * mod_stack is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

class modStackHelper
{

  /**
   * Retrieves a list of content items to display
   *
   * @param array $params An object containing the module parameters
   * @access public
   */
  public static function getList( &$params )
  {



    $db       = JFactory::getDbo();
    $user     = JFactory::getUser();
    $groups   = implode(',', $user->getAuthorisedViewLevels());

    // Preferences
    $categories = $params->get('categories');
    $not_categories = $params->get('not_categories');
    $tags       = $params->get('tags');
    $featured   = $params->get('featured_only', 0);
    $maximum    = $params->get('maximum', 5);
    $order      = $params->get('order', 0);
    $feature_first = $params->get('featured_first', 0);
    $direction  = $params->get('direction', 0);
    $template   = $params->get('template', 'Carousel');
    $use_js     = $params->get('use_js', 1);
    $use_css    = $params->get('use_css', 1);
    $truncate   = $params->get('truncate', 140);
    $itemid     = $params->get('itemid', false);
    $offset     = $params->get('offset', 0);

    $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
    $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

    // SELECT
    $qSelect = array();
    $qFrom = array();
    $qJoin = array();
    $qWhere = array();
    $qOrder = array();
    $qLimit = array();

    $qSelect[] = $db->quoteName( 'content.id', 'content_id' );
    $qSelect[] = $db->quoteName( 'content.title', 'content_title' );
    $qSelect[] = $db->quoteName( 'content.alias', 'content_alias' );
    $qSelect[] = $db->quoteName( 'content.introtext', 'content_introtext' );
    $qSelect[] = $db->quoteName( 'content.fulltext', 'content_fulltext' );
    $qSelect[] = $db->quoteName( 'content.images', 'content_images' );


    $qFrom[] = $db->quoteName('#__content', 'content');

    // join category alias to content item for router
    $qSelect[] = $db->quoteName( 'category.id', 'category_id');
    $qSelect[] = $db->quoteName( 'category.alias', 'category_alias');
    $qJoin[] = array('direction' => 'LEFT',
                     'table'     => $db->quoteName('#__categories', 'category'),
                     'on' => $db->quoteName('content.catid').' = '.$db->quoteName('category.id'));

    if ($categories)
    {
      if (count($categories) > 1) {
        $qWhere[] = '('.$db->quoteName('category.id').' = '.implode(' OR '.$db->quoteName('category.id').' = ', $categories).')';
      } else {
        $qWhere[] = $db->quoteName('category.id').' = '. $categories[0];
      }
    }



    if ($not_categories)
    {
      if (count($not_categories) > 1) {
        $qWhere[] = '('.$db->quoteName('category.id').' != '.implode(' AND '.$db->quoteName('category.id').' != ', $not_categories).')';
      } else {
        $qWhere[] = $db->quoteName('category.id').' != '. $not_categories[0];
      }
    }

    if ($tags)
    {
      $qJoin[] = array('direction' => 'LEFT',
                       'table'     => $db->quoteName('#__contentitem_tag_map', 'content_tag'),
                       'on'        => $db->quoteName('content_tag.content_item_id').' = '.$db->quoteName('content.id'));
      if (count($tags) > 1) {
        $qWhere[] = '('.$db->quoteName('content_tag.tag_id').' = '.implode(' OR '.$db->quoteName('content_tag.tag_id').' = ', $tags).')';
      }
      $qWhere[] = $db->quoteName('content_tag.tag_id').' = '. $tags[0];
    }

    // featured only
    if ($featured) {
      $qWhere[] = $db->quoteName('content.featured').' = 1';
    }

    // featured first
    if ($feature_first) {
      $qOrder[] = $db->quoteName('content.featured').' DESC';
    }


    // ordering
    if ($direction == 0) { $direction = 'ASC'; }
    if ($direction == 1) { $direction = 'DESC'; }
    if ($order == 0) { $order = 'ordering'; }
    if ($order == 1) { $order = 'title'; }
    if ($order == 2) { $order = 'created'; }
    if ($order == 3) { $order = 'publish_up'; }
    if ($order)
    {
      $qOrder[] = $db->quoteName('content.'.$order).' '.$direction;
    }

    $qLimit['offset'] = $offset;

    if ($maximum)
    {
      $qLimit['limit'] = $maximum;
    }


    $qJoins = array();
    foreach($qJoin as $join) {
      $qJoins[] = $join['direction'].' JOIN '.$join['table'].' ON '.$join['on'];
    }

/*     print_r($qSelect); */
/*     print_r($qFrom); */
/*     print_r($qJoins); */
/*     print_r($qWhere); */
/*     print_r($qOrder); */
/*     print_r($qLimit); */


    $query = '';
    if (!$qSelect) { return; }
    $query .= 'SELECT '.implode(',', $qSelect);
    if (!$qFrom) { return; }
    $query .= ' FROM '.implode(',', $qFrom);

    if ($qJoins) {
      $query .= ' '.implode(' ', $qJoins);
    }
    if ($qWhere) {
      $query .= ' WHERE '.implode(' AND ', $qWhere);
    }
    if ($qOrder) {
      $query .= ' ORDER BY '.implode(', ', $qOrder);
    }
    $query .= ' LIMIT '.implode(', ', $qLimit);




    // build query and return it
    $db->setQuery($query);


/*     var_dump($db->replacePrefix((string) $db->getQuery())); */
/*     exit; */

    $results = $db->loadObjectList();

    // findImage
    foreach($results as $index=>$item) {
      $results[$index]->introimage = modStackHelper::findImage($item);
    }

    // remove images from item
    foreach($results as $index=>$item) {
      $results[$index]->content_introtext = modStackHelper::removeImages($item->content_introtext);
    }

    // remove tags from item
    foreach($results as $index=>$item) {
      $results[$index]->content_introtext = strip_tags($item->content_introtext);
    }


    /*
     * process content length
     */
    if ($truncate > 0) {
      foreach($results as $index=>$item) {
        $results[$index]->content_introtext = modStackHelper::truncate($item->content_introtext, $truncate);
      }

    }



    foreach($results as $index=>$item) {
      $item->slug = $item->content_id . ':' . $item->content_alias;
        $item->catslug = $item->category_id . ':' . $item->category_alias;


			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article

        if ($itemid) {
          $item->link = modStackHelper::getArticleRoute($item->slug, $itemid, $item->catslug);
        } else {
          $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        }

      }
      else
      {
      	$item->link = JRoute::_('index.php?option=com_users&view=login');
      }

    }



    return $results;
  }

  public static function getArticleRoute($id, $itemid = 0, $catid = 0, $language = 0)
  {
    $needles = array(
			'article'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_content&view=article&id='. $id;
		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Content');
			$category = $categories->get((int) $catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}
		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}

		$link .= '&Itemid='.$itemid;

		return $link;

  }

  public static function truncate($text, $chars = 25)
  {
    $trunc = false;
    if (strlen($text) >= $chars) {
      $trunc = true;
    }


    $text = $text." ";
    $text = substr($text,0,$chars);
    $text = substr($text,0,strrpos($text,' '));

    if ($trunc == true) {
      $text = trim($text)."&hellip;";
    }



    return $text;
  }

  public static function findImage($item)
  {
    $images = json_decode($item->content_images);

    /*
    $image_intro
    $image_intro_alt
    $image_intro_caption

    $image_fulltext
    $image_fulltext_alt
    $image_fulltext_caption
    */

    $image_intro = null;

    // catch image field in database being empty completely
    if (!$images) {
      $images = new JObject;
      $images->setProperties(array('image_intro' => null, 'image_fulltext' => null));
    }

    // look for intro image
    if ($images->image_intro != '') {
      $image_intro = array('image' => $images->image_intro,
                           'alt'   => $images->image_intro_alt,
                           'caption' => $images->image_intro_caption);
    }

    // look for fulltext image
    else if ($images->image_fulltext != '') {
      $image_intro = array('image' => $images->image_fulltext,
                           'alt'   => $images->image_fulltext_alt,
                           'caption' => $images->image_fulltext_caption);
    }

    else {

      // find image in intro text
      $src = null;

      // <img[^>]+>
      // explode(' ', $text) // explode on spaces inside tag
      // explode('=', $text) // explode the
      // get src tag
      // trim($value, "'"); // strip quotes from value
      $pattern = '/<img[^>]+>/';
      $matches = null;


      if (!$matches) {
        preg_match($pattern, $item->content_introtext, $intro_matches);

        if ($intro_matches) {
          $matches = $intro_matches;
        }
      }



      if (!$matches) { // found one
        preg_match($pattern, $item->content_fulltext, $fulltext_matches);
        if ($fulltext_matches) {
          $matches = $fulltext_matches;
        }
      }

      if (!$matches) {
        return;
      }

      $match = $matches[0]; // only need one

      if (!$matches) {
        return;
      }

      $attributes = explode(' ', $match);
      foreach($attributes as $index=>$attribute) {
        $attributes[$index] = explode('=', $attribute);
      }

      foreach($attributes as $index=>$attribute) {
        if (in_array('src', $attribute)) {
          $src = $attribute[1];
        }
      }

      $image_intro = array('image' => trim($src, "'\""));
    }


    return $image_intro;

  }

  public static function removeImages($item)
  {
    return preg_replace('/<img[^>]+>/', '', $item);
  }
}