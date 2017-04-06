<?php

/**
 * @author jfcherng@gmail.com
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

        $form_title = $this->gettext('quota_plugin_title') . ' ::: ' . $rc->user->data['username'];
        $storage = $rc->get_storage();
        $quota = $storage->get_quota();

        if (!isset($quota['total'])) {
            $quota_text = $this->gettext('unknown');
        } else {
            $quota_text = sprintf(
                '%f %% ( %d KB / %d KB )',
                $quota['percent'],
                $quota['used'],
                $quota['total']
            );
        }

        $out =
            html::div(
                array('class' => 'box'),
                html::div(
                    array('id' => 'prefs-title', 'class' => 'boxtitle'),
                    $form_title
                ).
                html::div(
                    array('class' => 'boxcontent'),
                    html::p(
                        null,
                        $this->gettext('space_used') . $quota_text
                    ) .
                    html::p(
                        null,
                        $this->gettext('problem_please_contact') . '<br />' .
                        '<br />' .
                        'Debug: ' . print_r($quota, true)
                    )
                )
            );

        return $out;
    }

}

