<?php

final class quota extends rcube_plugin
{
    const ONE_KB = 1;
    const ONE_MB = 1024;
    const ONE_GB = 1048576;
    const ONE_TB = 1073741824;
    const ONE_PB = INF;

    /**
     * @var string
     */
    public $task = 'mail|settings';

    /**
     * The loaded configuration.
     *
     * @var rcube_config
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->load_plugin_config();

        $this->add_texts('localization/', true);
        $this->add_hook('quota', [$this, 'quota_message']);
        $this->add_hook('settings_actions', [$this, 'settings_actions']);

        $this->register_action('plugin.' . __CLASS__, [$this, 'quota_init']);

        $this->include_script('js/Chart-2.7.3.min.js');
        $this->include_script('js/draw.js');
    }

    public function settings_actions(array $args)
    {
        $args['actions'][] = [
            'action' => 'plugin.' . __CLASS__,
            'class' => 'quota',
            'label' => 'quota_plugin_title',
            'domain' => 'quota',
        ];

        return $args;
    }

    public function quota_init()
    {
        $rc = rcmail::get_instance();

        $this->register_handler('plugin.body', [$this, 'quota_form']);

        $rc->output->set_pagetitle($this->gettext('quota_plugin_title'));
        $rc->output->send('plugin');
    }

    public function quota_message(array $args)
    {
        $rc = rcmail::get_instance();

        $thresholds = [
            99 => 'error',
            90 => 'warning',
        ];

        krsort($thresholds);

        foreach ($thresholds as $percent => $level) {
            if ($args['percent'] >= $percent) {
                $rc->output->show_message($this->gettext("quota_meet_{$percent}"), $level);

                break;
            }
        }
    }

    public function quota_form()
    {
        $rc = rcmail::get_instance();

        $quota = $rc->get_storage()->get_quota();

        if (isset($quota['total'])) {
            $quotaUsedKb = $quota['used'];
            $quotaFreeKb = $quota['total'] - $quota['used'];
        } else {
            $quotaUsedKb = 0;
            $quotaFreeKb = self::ONE_PB;
        }

        $quotaTotalKb = $quotaUsedKb + $quotaFreeKb;
        $quotaUsedHumanized = $this->humanize_kb_quota($quotaUsedKb);
        $quotaFreeHumanized = $this->humanize_kb_quota($quotaFreeKb);
        $quotaTotalHumanized = $this->humanize_kb_quota($quotaTotalKb);

        if (isset($quota['total'])) {
            $quotaText = sprintf(
                '%.2f%% ( %s of %s )',
                $quota['percent'],
                $quotaUsedHumanized,
                $quotaTotalHumanized
            );
        } else {
            $quotaText = $this->gettext('unknown');
        }

        $out = (
            html::div(
                ['class' => 'box contentbox'],
                html::div(
                    ['id' => 'prefs-title', 'class' => 'boxtitle'],
                    $this->gettext('quota_plugin_title')
                ) .
                html::div(
                    ['class' => 'boxcontent'],
                    // debug information
                    (
                        $this->config->get('debug') ?
                            html::p(
                                ['id' => 'quotaPluginDebugInfo'],
                                (
                                    'dump $quota = ' . print_r($quota, true)
                                )
                            ) : ''
                    ) .
                    // text reprecentation
                    (
                        $this->config->get('enable_text_presentation') ?
                            html::p(
                                null,
                                $this->gettext('space_used') . ': ' . $quotaText
                            ) : ''
                    ) .
                    // chart reprecentation
                    (
                        $this->config->get('enable_chart_presentation')
                            ? '<canvas id="chartContainer" style="height: 370px; width: 100%; max-width: 600px;"></canvas>'
                            : ''
                    ) .
                    // admin contact
                    (
                        $this->config->get('show_admin_contact') ?
                            html::p(
                                null,
                                sprintf($this->gettext('problem_please_contact'), $this->config->get('admin_contact'))
                            ) : ''
                    )
                )
            )
        );

        $jsVars = [
            'charTitle' => $this->gettext('chart_title'),
            'labelUsedSpace' => $this->gettext('space_used'),
            'labelFreeSpace' => $this->gettext('space_free'),
            'quotaUsedKb' => $quotaUsedKb,
            'quotaFreeKb' => $quotaFreeKb,
            'quotaTotalKb' => $quotaTotalKb,
            'quotaUsedHumanized' => $quotaUsedHumanized,
            'quotaFreeHumanized' => $quotaFreeHumanized,
            'quotaTotalHumanized' => $quotaTotalHumanized,
        ];

        $out .= $this->config->get('enable_chart_presentation') ?
            '<script>
                var plugin_quota_chart_vars = ' . json_encode($jsVars, JSON_UNESCAPED_UNICODE) . ';

                drawDiskQuota();
            </script>' : '';

        $out = '<style> .contentbox { overflow: auto; } </style>' . $out;

        return $out;
    }

    private function humanize_kb_quota($quota, $round = 2)
    {
        $quota = (float) $quota;

        $units = [
            'PB' => self::ONE_PB,
            'TB' => self::ONE_TB,
            'GB' => self::ONE_GB,
            'MB' => self::ONE_MB,
            'KB' => self::ONE_KB,
        ];

        $partition = [self::ONE_KB, 'KB'];
        foreach ($units as $unit => $size) {
            if ($quota >= $size) {
                $partition = [$size, $unit];
                break;
            }
        }

        return round($quota / $partition[0], $round) . " {$partition[1]}";
    }

    /**
     * Load plugin configuration.
     */
    private function load_plugin_config()
    {
        $rcmail = rcmail::get_instance();

        $this->load_config('config.inc.php.dist');
        $this->load_config('config.inc.php');

        $this->config = $rcmail->config;
    }
}
