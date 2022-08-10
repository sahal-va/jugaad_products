<?php

namespace Drupal\jugaad_products\Plugin\Block;

use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a qr code block.
 *
 * @Block(
 *   id = "jugaad_products_qr_code",
 *   admin_label = @Translation("QR Code"),
 *   category = @Translation("Custom")
 * )
 */
class QrCodeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructs a new QrCodeBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\HttpFoundation\Request $request_stack
   *   The request stack.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'logo_width' => 350,
      'set_size' => 600,
      'set_margin' => 100,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['logo_width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Logo Width'),
      '#default_value' => $this->configuration['logo_width'],
    ];
    $form['set_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Size of the QR code'),
      '#default_value' => $this->configuration['set_size'],
    ];
    $form['set_margin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Margin'),
      '#default_value' => $this->configuration['set_margin'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['logo_width'] = $form_state->getValue('logo_width');
    $this->configuration['set_size'] = $form_state->getValue('set_size');
    $this->configuration['set_margin'] = $form_state->getValue('set_margin');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Set cache context as url as block should be cached per product page.
    // Added custom tag custom_block:qr_code, if needed to invalidate from somewhere.
    $cache = [
      'contexts' => ['url'],
      'tags' => ['custom_block:qr_code'],
    ];

    /** @var Drupal\node\Entity\Node $node */
    $node = $this->request->attributes->get('node');

    // Default markup for block, if it is not within a product page.
    $build['content'] = [
      '#type' => 'markup',
      '#markup' => $this->t('This block is not in product page :('),
    ];

    // Check 'node' parameter from url is of bundle 'product'.
    if (!empty($node) && $node->bundle() == 'product') {

      // Default markup for block, if app link is not given.
      $build['content'] = [
        '#type' => 'markup',
        '#markup' => $this->t('App Link is empty/invalid for this product :('),
      ];
      if ($app_link = $node->get('field_app_purchase_link')->value) {
        $data = UrlHelper::isValid($app_link);
        if ($data) {
          $option = [
            'query' => ['path' => $app_link],
          ];
          $uri = Url::fromRoute('jugaad_products.qr.url', [], $option)
            ->toString();
        }
        $build['content'] = [
          '#theme' => 'qr_code',
          '#attributes' => [
            'class' => 'qr_code-wrapper',
          ],
          '#attached' => [
            'library' => 'jugaad_products/jugaad_products',
          ],
        ];
        $build['content']['qr_code'] = [
          '#theme' => 'image',
          '#uri' => $uri,
          '#attributes' => ['class' => 'jugaad-product-qr-code'],
        ];
      }
      $cache['tags'] = Cache::mergeTags($cache['tags'], $node->getCacheTags());
    }

    // Set cache array.
    $build['content']['#cache'] = $cache;

    return $build;
  }

}
