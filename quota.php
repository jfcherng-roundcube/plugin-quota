<?php

class quota extends rcube_plugin
{
    /**
     * @var string
     */
    public $task = 'mail|settings';

    /**
     * The loaded configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->loadPluginConfig();

        $this->add_texts('localization/', true);
        $this->add_hook(__CLASS__, [$this, 'quotaMessage']);

        $this->register_action('plugin.' . __CLASS__, [$this, 'quotaInit']);

        $this->include_script('js/settings_sidebar.js');
        $this->include_script('js/echarts-4.1.0.rc2.common.min.js');
        $this->include_script('js/draw.js');
    }

    public function quotaInit()
    {
        $rc = rcmail::get_instance();

        $this->register_handler('plugin.body', [$this, 'quotaForm']);

        $rc->output->set_pagetitle($this->gettext('quota_plugin_title'));
        $rc->output->send('plugin');
    }

    public function quotaMessage(array $args)
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

    public function quotaForm()
    {
        $rc = rcmail::get_instance();

        $quota = $rc->get_storage()->get_quota();

        if (isset($quota['total'])) {
            $quotaText = sprintf(
                '%.2f%% ( %s of %s )',
                $quota['percent'],
                $this->humanizeKbQuota($quota['used']),
                $this->humanizeKbQuota($quota['total'])
            );
            $quotaUsedKb = $quota['used'];
            $quotaFreeKb = $quota['total'] - $quota['used'];
        } else {
            $quotaText = $this->gettext('unknown');
            $quotaUsedKb = 0;
            $quotaFreeKb = pow(1024, 3); // 1TB
        }

        $out = (
            html::div(
                ['class' => 'box'],
                html::div(
                    ['id' => 'prefs-title', 'class' => 'boxtitle'],
                    $this->gettext('quota_plugin_title')
                ) .
                html::div(
                    ['class' => 'boxcontent'],
                    // debug information
                    (
                        $this->config['debug'] ?
                            html::p(
                                ['id' => 'quotaPluginDebugInfo'],
                                (
                                    'dump $quota = ' . print_r($quota, true)
                                )
                            ) : ''
                    ) .
                    // text reprecentation
                    (
                        $this->config['enable_text_presentation'] ?
                            html::p(
                                null,
                                $this->gettext('space_used') . ': ' . $quotaText
                            ) : ''
                    ) .
                    // chart reprecentation
                    (
                        $this->config['enable_chart_presentation'] ?
                            html::p(
                                ['id' => 'chartContainer', 'style' => 'height: 370px; width: 100%; max-width: 600px;']
                            ) : ''
                    ) .
                    // admin contact
                    (
                        $this->config['show_admin_contact'] ?
                            html::p(
                                null,
                                sprintf($this->gettext('problem_please_contact'), $this->config['admin_contact'])
                            ) : ''
                    )
                )
            )
        );

        $out .= $this->config['enable_chart_presentation'] ?
            '<script>
                var plugin_quota_chart_vars = {
                    charTitle: "' . addslashes($this->gettext('chart_title')) . '",
                    labelUsedSpace: "' . addslashes($this->gettext('space_used')) . '",
                    labelFreeSpace: "' . addslashes($this->gettext('space_free')) . '",
                    quotaUsedKb: ' . $quotaUsedKb . ',
                    quotaFreeKb: ' . $quotaFreeKb . '
                };

                drawDiskQuota();
            </script>' : '';

        return $out;
    }

    protected function humanizeKbQuota($quota)
    {
        $quota = (float) $quota;

        $units = ['KB', 'MB', 'GB', 'TB', 'PB'];

        foreach ($units as $unit) {
            if ($quota < 1024) {
                return "{$quota} {$unit}";
            }

            $quota = round($quota / 1024, 2);
        }

        return $quota * 1024 . ' ' . end($units);
    }

    /**
     * Load plugin configuration.
     *
     * @return self
     */
    protected function loadPluginConfig()
    {
        $rc = rcmail::get_instance();

        $userPerf = $this->load_config('config.inc.php')
            ? $rc->config->all()
            : [];

        $this->load_config('config.inc.php.dist');
        $rc->config->merge($userPerf);

        $this->config = $rc->config->all();

        return $this;
    }
}
