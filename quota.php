<?php

final class quota extends rcube_plugin
{
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

        $this->add_plugin_assets();
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
        $RCMAIL = rcmail::get_instance();

        $this->register_handler('plugin.body', [$this, 'quota_form']);

        $RCMAIL->output->set_pagetitle($this->gettext('quota_plugin_title'));
        $RCMAIL->output->send('plugin');
    }

    public function quota_message(array $args)
    {
        $RCMAIL = rcmail::get_instance();

        $thresholds = [
            99 => 'error',
            90 => 'warning',
        ];

        krsort($thresholds);

        foreach ($thresholds as $percent => $level) {
            if ($args['percent'] >= $percent) {
                $RCMAIL->output->show_message($this->gettext("quota_meet_{$percent}"), $level);

                break;
            }
        }
    }

    public function quota_form()
    {
        $RCMAIL = rcmail::get_instance();
        $STORAGE = $RCMAIL->get_storage();

        $quota = $STORAGE->get_quota();

        if (isset($quota['total'])) {
            $quota_used_kb = $quota['used'];
            $quota_free_kb = $quota['total'] - $quota['used'];
        } else {
            $quota_used_kb = 0;
            $quota_free_kb = \INF;
        }

        $quota_total_kb = $quota_used_kb + $quota_free_kb;
        $quota_used_humanized = $RCMAIL->show_bytes($quota_used_kb * 1024);
        $quota_free_humanized = $RCMAIL->show_bytes($quota_free_kb * 1024);
        $quota_total_humanized = $RCMAIL->show_bytes($quota_total_kb * 1024);

        if (isset($quota['total'])) {
            $quota_text = sprintf(
                '%.2f%% ( %s of %s )',
                $quota['percent'],
                $quota_used_humanized,
                $quota_total_humanized
            );
        } else {
            $quota_text = $this->gettext('unknown');
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
                                ['id' => 'quota-plugin-debug-info'],
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
                                $this->gettext('space_used') . ': ' . $quota_text
                            ) : ''
                    ) .
                    // chart reprecentation
                    (
                        $this->config->get('enable_chart_presentation')
                            ? '<canvas id="chart-container" style="height: 370px; width: 100%; max-width: 600px;"></canvas>'
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

        $js_variables = [
            'char_title' => $this->gettext('chart_title'),
            'label_used_space' => $this->gettext('space_used'),
            'label_free_space' => $this->gettext('space_free'),
            'quota_used_kb' => $quota_used_kb,
            'quota_free_kb' => $quota_free_kb,
            'quota_total_kb' => $quota_total_kb,
            'quota_used_humanized' => $quota_used_humanized,
            'quota_free_humanized' => $quota_free_humanized,
            'quota_total_humanized' => $quota_total_humanized,
        ];
        $js_variables_encoded = json_encode($js_variables, \JSON_UNESCAPED_UNICODE | \JSON_FORCE_OBJECT);

        $out .= $this->config->get('enable_chart_presentation') ?
            "<script>
                // exposed by plugin: quota
                var plugin_quota_chart_vars = {$js_variables_encoded};

                drawDiskQuota();
            </script>" : '';

        $out = '<style> .contentbox { overflow: auto; } </style>' . $out;

        return $out;
    }

    /**
     * Add plugin assets.
     */
    private function add_plugin_assets()
    {
        $this->include_stylesheet($this->local_skin_path() . '/main.css');

        $this->include_script('assets/Chart-2.7.3.min.js');
        $this->include_script('assets/draw.min.js');
    }

    /**
     * Load plugin configuration.
     */
    private function load_plugin_config()
    {
        $RCMAIL = rcmail::get_instance();

        $this->load_config('config.inc.php.dist');
        $this->load_config('config.inc.php');

        $this->config = $RCMAIL->config;
    }
}
