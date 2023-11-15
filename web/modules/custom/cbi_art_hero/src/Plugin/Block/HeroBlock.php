<?php

namespace Drupal\cbi_art_hero\Plugin\Block;

use Drupal\Core\Block\BlockBase;
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
      $output = [
        '#image' => $this->getRenderedMedia($node),
        '#title' => $node->getTitle(),
        '#abstract' => SafeFieldGetter::firstSimple($node, 'field_teaser_text', 'no teaser'),
        '#genre' => $this->getListOfGenre($node),
      ];

      $output['#theme'] = 'hero_block';

      $output['#attached']['library'][] = 'cbi_art_hero/hero_styles';
		}
		return $output;
	}

  protected function getRenderedMedia(NodeInterface $node): array {
    $media = SafeFieldGetter::firstReference($node, 'field_media');
    if (!($media instanceof Media)) {
      // Always return an array.
      return [];
    }
    $builder = \Drupal::entityTypeManager()->getViewBuilder('media');
    return $builder->view($media, 'default');
  }

  protected function getListOfGenre(NodeInterface $node): array{
    $genres = SafeFieldGetter::allReferences($node, 'field_categories', NULL);
    if (empty($genres)) {
      return [];
    }
    $results = [];
    foreach ($genres as $genre) {
      // Build a new item. @todo
    }
    return $results;
  }

}
