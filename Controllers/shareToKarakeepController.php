<?php
declare(strict_types=1);

use ShareToKarakeep\Models\View;

final class FreshExtension_ShareToKarakeep_Controller extends Minz_ActionController
{
    public ?Minz_Extension $extension;

    /**
     * @var View
     * @phpstan-ignore property.phpDocType
     */
    protected $view;

    public function __construct()
    {
        parent::__construct(View::class);
    }

    public function init(): void
    {
        $this->extension = Minz_ExtensionManager::findExtension('Share to Karakeep');
    }

    public function shareAction(): void
    {
        if (!FreshRSS_Auth::hasAccess()) {
            Minz_Error::error(403);
        }

        $id = Minz_Request::paramString('id');
        if ($id === '') {
            Minz_Error::error(404);
        }

        $entryDAO = FreshRSS_Factory::createEntryDao();
        $entry = $entryDAO->searchById($id);
        if ($entry === null) {
            Minz_Error::error(404);
            return;
        }
        $this->view->entry = $entry;

        Minz_View::prependTitle(_t('share_to_karakeep.share.title') . ' · ');
        $this->view->_layout('simple');

        if (Minz_Request::isPost()) {
            $conf = FreshRSS_Context::userConf();
            $karakeepUrl = $conf->karakeep_url;
            $karakeepToken = $conf->karakeep_token;

            if (empty($karakeepUrl) || empty($karakeepToken)) {
                Minz_Request::bad(_t('share_to_karakeep.share.feedback.not_configured'), [
                    'c' => 'shareToKarakeep',
                    'a' => 'share',
                    'params' => ['id' => $id],
                ]);
                return;
            }

            $posted_archived = Minz_Request::paramBoolean('karakeep_archived');
            $posted_favorited = Minz_Request::paramBoolean('karakeep_favorited');

            # https://github.com/FreshRSS/FreshRSS/blob/afa7c8440f336b603c051416bdc3809af4600725/lib/Minz/Log.php#L106
            Minz_Log::debug("Karakeep-ext - posted_archived: {$posted_archived}, posted_favorited: {$posted_favorited}");

            # https://docs.karakeep.app/api/create-a-new-bookmark
            $apiUrl = rtrim($karakeepUrl, '/') . '/api/v1/bookmarks';
            $payload = json_encode([
                'type' => 'link',
                'source' => 'api',
                'url' => $entry->link(),
                # Let Karakeep derive title
                #'title' => $entry->title(),
                'archived' => $posted_archived,
                'favourited' => $posted_favorited,
            ]);

            $headers = [
                'Authorization: Bearer ' . $karakeepToken,
                'Content-Type: application/json',
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_error($ch)) {
                Minz_Log::error("Failed to call Karakeep: " . curl_error($ch));
                return;
            }
            $response_json = json_decode($response, true);

            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response_code == 200) {
                Minz_Request::good(_t('share_to_karakeep.share.feedback.existed'), [
                    'c' => 'index',
                    'a' => 'index',
                ]);
            } elseif ($response_code == 201) {
                Minz_Request::good(_t('share_to_karakeep.share.feedback.created'), [
                    'c' => 'index',
                    'a' => 'index',
                ]);
            } else {
                Minz_Request::bad(_t('share_to_karakeep.share.feedback.failed'), [
                    'c' => 'shareToKarakeep',
                    'a' => 'share',
                    'params' => ['id' => $id],
                ]);
                Minz_Log::error("Failed to save [{$entry->link()}]. Message: {$response_json["message"]}");
            }
        }
    }
}
