<?php // no direct acces
defined( '_JEXEC' ) or die( 'Restricted access');

$document = JFactory::getDocument();

if ($params->get('use_js', true)) {
  $document->addScript('modules/mod_stack/media/js/devUtility.js');
/*   $document->addScript(JURI::base() . 'media/mod_stack/js/picturefill.js'); */
  $document->addScript('modules/mod_stack/media/js/responsiveSlider.js');
}

if ($params->get('use_css', true)) {
  $document->addStyleSheet(JURI::base() . 'media/mod_stack/css/mod_grid.css');
}

$template   = $params->get('template', 'Carousel');
$moduleclass_sfx = $params->get('moduleclass_sfx', $template);


$breakpoint = $params->get('breakpoint', 480);
$breakpoint_images = $params->get('breakpoint_images', array(480, 768));
$item_count = count($list);


$style = '
@media (min-width: '.$breakpoint.'px) {

  .'.$moduleclass_sfx.' .'.$moduleclass_sfx.'__container {
    transition: margin 0.3s;
    display: block;
    margin-left: 0px;
    width: '. 100 * $item_count . '% !important;
  }

  .'.$moduleclass_sfx.' .'.$moduleclass_sfx.'__item {
    float: left;
    margin: 0px;
    padding: 0px;
    width: ' . 100 / $item_count . '%;
  }

}';

$style_actions  = '@media (min-width: '.$breakpoint.'px) {'."\n";
foreach($list as $index=>$item) {
  $style_actions .= '#'.$moduleclass_sfx.'__item' . $index . ':checked ~ .'.$moduleclass_sfx.'__viewport .'.$moduleclass_sfx.'__container { margin-left: ' . ($index * 100 * -1) .'%; }'."\n";
}
$items = array();
foreach($list as $index=>$item) {
  $item_index = '#' . $moduleclass_sfx . '__item' . $index . ':checked ~ .'.$moduleclass_sfx.'__nav .'.$moduleclass_sfx.'__nav-item[for='.$moduleclass_sfx.'__item' . $index.']';
  $items[] = $item_index;
}


$style_actions .= implode(','."\n", $items) . '{ text-decoration: underline; }';
$style_actions .= '}';

$document->addStyleDeclaration( $style );
$document->addStyleDeclaration( $style_actions );


?>



<div class="js-carousel" id="<?php echo $moduleclass_sfx; ?>">

  <?php foreach($list as $index=>$item) : ?>
  <input type="radio" name="<?php echo trim($moduleclass_sfx); ?>" id="<?php echo $moduleclass_sfx; ?>__item<?php echo $index; ?>" class="<?php echo trim($moduleclass_sfx); ?>__radio-control" <?php if ($index == 0) : ?>checked<?php endif; ?> />
  <?php endforeach; ?>

  <div class="<?php echo $moduleclass_sfx; ?>__viewport">
    <div class="<?php echo $moduleclass_sfx; ?>__container">

      <?php foreach($list as $index=>$item) : ?>

      <article class="<?php echo $moduleclass_sfx; ?>__item">
        <figure class="<?php echo trim($moduleclass_sfx); ?>__figure">
          <div class="<?php echo trim($moduleclass_sfx); ?>__img-wrapper">
            <img src="<?php echo $item->introimage['image']; ?>" alt="<?php echo $item->introimage['alt']; ?>"/>
          </div>

          <figcaption class="<?php echo trim($moduleclass_sfx); ?>__caption">
            <?php if ($params->get('show_title', true)) : ?>
              <h1 class="<?php echo trim($moduleclass_sfx); ?>__title"><?php echo $item->content_title; ?></h1>
            <?php endif; ?>

            <?php if ($params->get('show_intro-text', true)) : ?>
              <p class="<?php echo trim($moduleclass_sfx); ?>__text"><?php echo $item->content_introtext; ?></p>
            <?php endif; ?>

            <?php if ($params->get('show_read-more', true)) : ?>
              <a class="<?php echo trim($moduleclass_sfx); ?>__link" href="<?php echo $item->link; ?>">Read More&hellip;</a>
            <?php endif; ?>
          </figcaption>
        </figure>
      </article>

      <?php endforeach; ?>

    </div>
  </div>

  <nav class="<?php echo trim($moduleclass_sfx); ?>__nav">

    <h3 class="section-header"><?php echo $module->title; ?> Navigation</h3>
    <ul>



    <li class="<?php echo trim($moduleclass_sfx); ?>__nav-previous">
      <button id="<?php echo trim($moduleclass_sfx); ?>__nav-previous"  class="<?php echo trim($moduleclass_sfx); ?>__nav-previous    js-<?php echo trim($moduleclass_sfx); ?>-button-previous"><span>Previous Slide</span></button></li>
    <?php foreach($list as $index=>$item) : ?>
      <li class="<?php echo trim($moduleclass_sfx); ?>__nav-item"><label for="<?php echo $moduleclass_sfx; ?>__item<?php echo $index; ?>" class="<?php echo trim($moduleclass_sfx); ?>__nav-item"><?php echo $index + 1; ?></label></li>
    <?php endforeach; ?>
    <li class="<?php echo trim($moduleclass_sfx); ?>__nav-next"><button id="<?php echo trim($moduleclass_sfx); ?>__nav-next"  class="<?php echo trim($moduleclass_sfx); ?>__nav-next    js-<?php echo trim($moduleclass_sfx); ?>-button-next"><span>Next Slide</span></button></li>
</ul>

  </nav>
</div>
<script>
  var responsiveSlider = new ResponsiveSlider({
       'element': document.getElementById('<?php echo trim($moduleclass_sfx); ?>'),
        'radios': document.getElementsByClassName('<?php echo trim($moduleclass_sfx); ?>__radio-control'),
    'nextButton': document.getElementById('<?php echo trim($moduleclass_sfx); ?>__nav-next'),
'previousButton': document.getElementById('<?php echo trim($moduleclass_sfx); ?>__nav-previous'),
      'interval': <?php echo $params->get('interval', 5) * 1000; ?>
  }).action('start');
</script>