<?php

namespace Drupal\cbi_art_hero\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\media\Entity\Media;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\safe_field_getter\SafeFieldGetter;

/**
 * Provides a Hero Block.
 *
 * @Block(
 *   id = "hero_block",
 *   admin_label = @Translation("Hero block"),
 *   category = @Translation("Custom"),
 * )
 */
class HeroBlock extends BlockBase
{
	/**
	 * {@inheritdoc}
	 */
	public function build()
	{
		$output = [];

		$node = \Drupal::routeMatch()->getParameter('node');
		if ($node instanceof NodeInterface) {
      // Watch for flag to build Hero.  Skip if missing.
      if (!SafeFieldGetter::firstBoolean($node, 'field_use_hero_block', FALSE)) {
        // No hero for you.
        return [];
      }
      $output = [
        '#image' => $this->getRenderedMedia($node),
        '#title' => $node->getTitle(),
        '#abstract' => SafeFieldGetter::firstSimple($node, 'field_teaser_text', 'no teaser'),

      ];
      if ($node->hasField('field_category')) {
        $output['#genre'] = $node->field_category->view('card');
      }

      $output['#theme'] = 'hero_block';

      $output['#attached']['library'][] = 'cbi_art_hero/hero_styles';
		}
		return $output;
	}

  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

  protected function getRenderedMedia(NodeInterface $node): array {
    $media = SafeFieldGetter::firstReference($node, 'field_media');
    if (!($media instanceof Media)) {
      // Always return an array.
      return [];
    }
    $builder = \Drupal::entityTypeManager()->getViewBuilder('media');
    return $builder->view($media, 'card');
  }
}
