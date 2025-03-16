<?php

namespace matthiasott\internetarchive\controllers;

use Craft;
use craft\web\Controller;
use craft\web\Response;
use matthiasott\internetarchive\services\InternetArchiveService;

class EntriesController extends Controller
{
    public function actionSaveAllUrls(): Response
    {
        $this->requirePostRequest();

        $service = new InternetArchiveService();
        $urls = $service->saveAllUrls();

        return $this->redirectToPostedUrl();
    }
}