<?php
/* @var $user_id int */
/* @var $active_tab [] */

if (!isset($active_tab)) {
    $active_tab['default'] = true;
}

$tabs = array_merge([
    'quotes' => [
        'label' => Yii::t('layout', 'Котировки'),
        'url' => ['/export/quote']
    ],
    'default' => [
        'label' => Yii::t('user', 'Анализ котировок'),
        'url' => ['/quotes/default/index']
    ],
    'quote-criteria' => [
        'label' => Yii::t('user', 'Критерии мониторинга'),
        'url' => ['/quotes/quote-criteria/index']
    ],
    'quote-journal' => [
        'label' => Yii::t('user', 'Журнал'),
        'url' => ['/quotes/quote-journal/index']
    ],
    'quote-history' => [
        'label' => Yii::t('quotes', 'История котировок'),
        'url' => ['/quotes/quote-history/index']
    ],
    'source-quote-history' => [
        'label' => Yii::t('quotes', 'История источников котировок'),
        'url' => ['/quotes/source-quote-history/index']
    ],
], ($asset_id ?? false)
    ? [
        'settings' => [
            'label' => Yii::t('layout', 'Настройки'),
            'url' => ['/quotes/settings/update', 'asset_id' => $asset_id],
            'linkOptions' => [
                'class' => 'btn-main-modal',
            ],
        ]
    ]
    : []
);

?>

<?= \backend\widgets\Tabs::initWidgetTabs($tabs, $active_tab, $widget_content);
