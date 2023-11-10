<?php

namespace Drupal\cbi_hero_banner\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\paragraphs\Entity\Paragraph;

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
		if ($node instanceof \Drupal\node\NodeInterface) {
			$has_field_hero_banner = $node->hasField('field_hero_banner');
			if ($has_field_hero_banner) {
				$paragraphs = $node->field_hero_banner->referencedEntities();

				$has_paragraph = count($paragraphs) > 0;
				if ($has_paragraph) {
					/**
					 * @var Paragraph hero_paragraph
					 */
					$hero_paragraph = $paragraphs[0];

					$builder = \Drupal::entityTypeManager()->getViewBuilder('paragraph');
					$output = $builder->view($hero_paragraph, 'default');
				}
			}
		}

		$output['#attached'] = [
			'library' => [
				'cbi_hero_banner/hero_styles',
			]
		];

		return $output;
	}
}
