<?php

/**
 * Original plugin creator:
 * @author jfcherng@gmail.com
 *
 * Added: usage pie-chart graphic, usage percent, documentation, BG translation:
 * @author Victor Kirov <victor.kirov.eu@gmail.com>
 */

class quota extends rcube_plugin
{
    public $task = 'mail|settings';

    public function init()
    {
        $this->add_texts('localization/', true);
        $this->add_hook('quota', array($this, 'quota_message'));
        $this->register_action('plugin.quota', array($this, 'quota_init'));
        $this->include_script('quota.js');
        $this->include_script('canvasjs.min.js');
        $this->include_script('canvasjs.chart.js');
    }

    public function quota_init()
    {
        $rc = rcmail::get_instance();

        $this->register_handler('plugin.body', array($this, 'quota_form'));
        $rc->output->set_pagetitle($this->gettext('quota_plugin_title'));
        $rc->output->send('plugin');
    }

    public function quota_message($args)
    {
        $rc = rcmail::get_instance();

        if ($args['percent'] >= 99) {
            $rc->output->show_message($this->gettext('quota_meet_99'), 'error');
        } elseif ($args['percent'] >= 90) {
            $rc->output->show_message($this->gettext('quota_meet_90'), 'warning');
        }
    }

    public function quota_form()
    {
        $rc = rcmail::get_instance();

        $form_title = $this->gettext('quota_plugin_title');
        $storage = $rc->get_storage();
        $quota = $storage->get_quota();

        if (!isset($quota['total'])) {
            $quota_text = $this->gettext('unknown');
        } else {
            $quota_text = sprintf('%f %% ( ', $quota['percent']);
            if (intval($quota['used']) < 1024) {
                $quota_text .= (round(floatval($quota['used']), 2)) . " KB of ";
            } else {
                $quota_text .= (round(floatval($quota['used']) / 1024, 2)) . " MB of ";
            }

            $quota_text .= (floatval($quota['total']) / 1024) . " MB )";
        }

        $quota1percent = floatval($quota['total']) / 100;
        $quotaUsedPercents = floatval($quota['used']) / $quota1percent;
        $quotaFreePercents = 100 - $quotaUsedPercents;

        $out = (
            html::div(
                array('class' => 'box'),
                html::div(
                    array('id' => 'prefs-title', 'class' => 'boxtitle'),
                    $form_title
                ) .
                html::div(
                    array('class' => 'boxcontent'),
                    html::p(
                        null,
                        $this->gettext('space_used') . $quota_text
                    ) .
                    html::div(
                        array('id' => 'chartContainer', 'style' => 'height: 370px; width: 100%;'),
                        ''
                    ) .
                    html::div(
                        array('id' => 'quotaUsedPercents', 'style' => 'display:none'),
                        $quotaUsedPercents
                    ) .
                    html::div(
                        array('id' => 'quotaFreePercents', 'style' => 'display:none'),
                        $quotaFreePercents
                    ) .
                    html::div(
                        array('id' => 'usedSpace', 'style' => 'display:none'),
                        $this->gettext('space_used')
                    ) .
                    html::div(
                        array('id' => 'freeSpace', 'style' => 'display:none'),
                        $this->gettext('space_free')
                    )
                )
            )
        );

        if (isset($quota['total'])) {
            $out .= '<script>drawDiskQuota();</script>';
        }

        return $out;
    }

}
