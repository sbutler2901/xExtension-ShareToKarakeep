<?php
declare(strict_types=1);

final class ShareToKarakeepExtension extends Minz_Extension {
    public function init(): void {
        $this->registerTranslates();

        $this->registerController('shareToKarakeep');
        $this->registerViews();

        $conf = FreshRSS_Context::userConf();
        $karakeep_url = $conf->karakeep_url;
        $karakeep_token = $conf->karakeep_token;
        if (!empty($karakeep_url) && !empty($karakeep_token)) {
            FreshRSS_Share::register([
                'type' => 'karakeep',
                'url' => Minz_Url::display(['c' => 'shareToKarakeep', 'a' => 'share']) . '&id=~ID~',
                'transform' => [],
                'form' => 'simple',
                'method' => 'GET',
            ]);
        }

        spl_autoload_register(array($this, 'loader'));
    }

    public function handleConfigureAction(): void {
        $this->registerTranslates();

        if (Minz_Request::isPost()) {
            $conf = FreshRSS_Context::userConf();
            $conf->karakeep_url = Minz_Request::paramString('karakeep_url');
            $conf->karakeep_token = Minz_Request::paramString('karakeep_token');
            $conf->save();
        }
    }

    public function loader(string $class_name): void {
        if (strpos($class_name, 'ShareToKarakeep') === 0) {
            $class_name = substr($class_name, 18);
            $base_path = $this->getPath() . '/';
            include($base_path . str_replace('\\', '/', $class_name) . '.php');
        }
    }
}
