<?php

namespace App;

use Exception;

/**
 * This class allow you to send email easily
 * Mail::init()
    ->from(Oseus . " <" . email@example.com . ">")
    ->to("amail@example.com")
    ->subject('lorem ipsum')
    ->template("/resources/mails/inscription", $data)
    ->send();
 */
class Mail
{
    public $to = array();
    public $cc = array();
    private $bcc = array();
    private $headers = array();
    private $attachments = array();
    public $sendAsHTML = true;
    public $subject = '';
    public $from = '';

    private $template = false;
    private $variables = array();

    public function __construct($params = [])
    {
        if ($params) {
            $this->to = $params['to'] ? $params['to'] : [];
            $this->from = $params['from'] ? $params['from'] : config('email');
            $this->sendAsHTML = $params['sendAsHTML'] ? $params['sendAsHTML'] : true;
        }
    }

    public static function init()
    {
        return new Self;
    }

    public function to($to)
    {
        if (is_array($to)) {
            $this->to = $to;
        } else {
            $this->to = array($to);
        }
        return $this;
    }

    /**
     * Set From header
     * @param  String
     * @return Object $this
     */
    public function from($from)
    {
        $this->from = $from;
        return $this;
    }
    /**
     * Set email Subject
     * @param  Srting $subject
     * @return Object $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function cc($cc)
    {
        if (is_array($cc)) {
            $this->cc = $cc;
        } else {
            $this->cc = array($cc);
        }
        return $this;
    }

    /**
     * Set the email's headers
     * @param  String|Array  $headers [description]
     * @return Object $this
     */
    public function headers($headers)
    {
        if (is_array($headers)) {
            $this->headers = $headers;
        } else {
            $this->headers = array($headers);
        }

        return $this;
    }

    /**
     * Set the template file
     * @param  String $template  Path to HTML template
     * @param  Array  $variables
     * @throws Exception
     * @return Object $this
     */
    public function template($template, $variables = null)
    {
        if (is_array($variables)) {
            $this->variables = $variables;
        }

        $this->template = $template;
        return $this;
    }

    /**
     * Attach a file or array of files.
     * Filepaths must be absolute.
     * @param  String|Array $path
     * @throws Exception
     * @return Object $this
     */
    public function attach($path)
    {
        if (is_array($path)) {
            $this->attachments = array();
            foreach ($path as $path_) {
                if (!file_exists($path_)) {
                    throw new Exception("Attachment not found at $path");
                } else {
                    $this->attachments[] = $path_;
                }
            }
        } else {
            if (!file_exists($path)) {
                throw new Exception("Attachment not found at $path");
            }
            $this->attachments = array($path);
        }

        return $this;
    }

    /**
     * Returns email content type
     * @return String
     */
    public function HTMLFilter()
    {
        return 'text/html';
    }

    public function parseAsMustache($string, $variables = array())
    {

        preg_match_all('/\{\{\s*.+?\s*\}\}/', $string, $matches);

        foreach ($matches[0] as $match) {
            $var = str_replace('{', '', str_replace('}', '', preg_replace('/\s+/', '', $match)));

            if (isset($variables[$var]) && !is_array($variables[$var])) {
                $string = str_replace($match, $variables[$var], $string);
            }
        }

        return $string;
    }

    /**
     * Builds Email Headers
     * @return String email headers
     */
    public function buildHeaders()
    {
        $headers = '';

        $headers .= implode("\r\n", $this->headers) . "\r\n";

        foreach ($this->bcc as $bcc) {
            $headers .= sprintf("Bcc: %s \r\n", $bcc);
        }

        foreach ($this->cc as $cc) {
            $headers .= sprintf("Cc: %s \r\n", $cc);
        }

        if (!empty($this->from)) {
            $headers .= sprintf("From: %s \r\n", $this->from);
        }

        return $headers;
    }

    public function buildSubject()
    {
        return $this->parseAsMustache(
            $this->subject,
            $this->variables
        );
    }

    /**
     * Renders the template
     * @return String
     */
    public function render()
    {
        $data = array_merge($this->variables, [
            'subject' => $this->subject,
            'to' => $this->to,
            'from' => $this->from,
        ]);

        $html = view($this->template, $data);

        return $html;
    }

    /**
     * Sends a rendered email using
     * WordPress's wp_mail() function
     * @return Bool
     */
    public function send()
    {

        if (count($this->to) === 0) {
            throw new Exception('You must set at least 1 recipient');
        }

        if (empty($this->template)) {
            throw new Exception('You must set a template');
        }

        if ($this->sendAsHTML) {
            add_filter('wp_mail_content_type', array($this, 'HTMLFilter'));
        }

        return wp_mail($this->to, $this->buildSubject(), $this->render(), $this->buildHeaders(), $this->attachments);
    }
}
