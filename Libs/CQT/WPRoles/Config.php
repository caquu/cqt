<?php
class CQT_WPRoles_Config
{
    /**
     * プラグインのサーバーパス
     * @var string
     */
    private $plugin_dir = '';

    /**
     * プラグインのURI
     * @var string
     */
    private $plugin_url = '';


    /**
     * debugフラグ
     * @var boolean
     */
    private $debug = false;


    /**
     *
     * @param string $path プラグインのサーバーパス
     * @param string $url　プラグインのURI
     */
    public function __construct($path, $url)
    {
        $this->setDir($path);
        $this->setURL($url);
    }

    /**
     * プラグインのサーバーパスを設定
     *
     * @param string $path
     */
    public function setDir($path)
    {
        $this->plugin_dir = $path;
    }

    /**
     * プラグインのURIを設定
     *
     * @param string $url
     */
    public function setURL($url)
    {
        $this->plugin_url = $url;
    }

    public function getDirecroty()
    {
        return $this->plugin_dir;
    }

    public function getURL()
    {
        return $this->plugin_url;
    }

    public function getRolesDirectory()
    {
        return $this->plugin_dir . 'roles' . DS;
    }

    public function debug($flag = true)
    {
        $this->debug = $flag;
    }

    /**
     * debugモードの判定
     *
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

}