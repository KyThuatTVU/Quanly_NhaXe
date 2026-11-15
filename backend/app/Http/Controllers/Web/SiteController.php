<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ContactService;
use App\Domain\Services\SettingService;
use App\Support\Flash;

final class SiteController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly ContactService $contacts,
    ) {
        parent::__construct($settings);
    }

    public function about(Request $request): Response
    {
        return $this->view('web/about/index', ['title' => 'Giới thiệu']);
    }

    public function contact(Request $request): Response
    {
        return $this->view('web/contact/index', ['title' => 'Liên hệ']);
    }

    public function sendContact(Request $request): Response
    {
        $this->contacts->create($request->all(), $request);
        Flash::put('success', 'Gửi liên hệ thành công, chúng tôi sẽ phản hồi sớm nhất');
        return $this->redirect('/lien-he');
    }
}
