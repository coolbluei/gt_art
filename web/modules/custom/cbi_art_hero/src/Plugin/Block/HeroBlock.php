<?php

namespace Drupal\cbi_art_hero\Plugin\Block;

use Drupal\Core\Block\BlockBase;
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
      $output['data'] = [
        'title' => $node->getTitle(),
        'abstract' => SafeFieldGetter::firstSimple($node, 'field_teaser', 'no teaser'),
      ];

      $output['#theme'] = 'hero_block';

      $output['#attached']['library'][] = 'cbi_art_hero/hero_styles';
		}
		return $output;
	}
}
