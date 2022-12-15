<?php
declare(strict_types=1);

namespace ModalForm\View\Helper;

use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\View\View;
use ModalForm\ModalFormPlugin;

/**
 * FormModal helper
 * 
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class ModalFormHelper extends Helper
{
    /**
     * helpers
     *
     * @var array
     */
    protected $helpers = ['Html', 'Url'];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [
        'modalTemplate' => ModalFormPlugin::MODAL_TEMPLATE,
        'formType' => 'POST',
        'element' => ModalFormPlugin::FORM_CHECKBOX,
        'templates' => [
            //'message' => '<div class="alert alert-light message">{{content}}</div>',
        ]
    ];

    protected $modalScript = <<<MODAL_SCRIPT
    $('#:target').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget)
        let modal = $(this)
        modal.find('form').prop('action', button.data('url'));
        modal.find('.message').html(button.data('confirm'));
    })
    MODAL_SCRIPT;

    public function addModal(string $target, array $options = []): string
    {
        $modalTemplate = $options['modalTemplate'] ?? $this->getConfig('modalTemplate');
        $modalScript = $options['modalScript'] ?? $this->modalScript;

        $this->Html->scriptBlock(Text::insert($modalScript, [
            'target' => $target,
        ]), ['block' => true]);

        return $this->getView()->element($modalTemplate, [
            'target' => $target,
            'formType' => $options['formType'] ?? $this->getConfig('formType'),
            'element' => $options['element'] ?? $this->getConfig('element'),
            'title' => $options['title'] ?? null,
        ]);
    }

    public function link($title, $url = null, array $options = []): string
    {
        $options['data-url'] = $this->Url->build($url);
        $options['data-toggle'] = 'modal';
        $options['data-target'] = '#' . $options['target']; unset($options['target']);
        $options['data-confirm'] = $options['confirm'] ?? null; unset($options['confirm']);

        return $this->Html->link($title, '#', $options);
    }

}