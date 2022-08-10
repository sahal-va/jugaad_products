<?php

namespace Drupal\jugaad_products\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'Jugaad  Qr Code' formatter.
 *
 * @FieldFormatter(
 *   id = "jugaad_products_jugaad_qr_code",
 *   label = @Translation("Jugaad Qr Code"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class JugaadQrCodeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the generated Qr Code.');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    foreach ($items as $delta => $item) {
      // Render each element as markup.
      if (!$item->isEmpty()) {
        $data = UrlHelper::isValid($item->getValue()['value']);
        if ($data) {
          $option = [
            'query' => ['path' => $item->getValue()['value']],
          ];
          $uri = Url::fromRoute('jugaad_products.qr.url', [], $option)
            ->toString();
        }
        else {
          $uri = Url::fromRoute('jugaad_products.qr.generator', [
            'content' => $item->getValue()['value'],
          ])->toString();
        }
        $element[$delta] = [
          '#theme' => 'image',
          '#uri' => $uri,
          '#attributes' => ['class' => 'module-name-center-image'],
        ];
      }
    }

    return $element;
  }

}
