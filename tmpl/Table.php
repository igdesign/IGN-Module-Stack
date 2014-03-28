<?php // no direct acces
defined( '_JEXEC' ) or die( 'Restricted access');

$document = JFactory::getDocument();

$template   = $params->get('template', 'Table');
$moduleclass_sfx = $params->get('moduleclass_sfx', $template);
?>
<table>
  <?php foreach($list as $index=>$item) : ?>
  <tbody>
    <tr>
      <?php if ($item->introimage['image']) : ?>
      <td valign="top" rowspan="2" width="25%"><img src="<?= $item->introimage['image']; ?>" alt="<?= $item->introimage['alt']; ?>" width="100%" /></td>
      <?php endif; ?>
      <td align="left" ><strong><?= $item->title; ?></strong></td>
    </tr>
    <tr>
      <td align="left" ><?= $item->introtext; ?></td>
    </tr>

  </tbody>
  <?php endforeach; ?>
</table>