<?php

use AmoCRM\OAuth2\Client\Provider\AmoCRM;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\CustomFields\CheckboxCustomFieldModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Exceptions\AmoCRMApiException;

include_once __DIR__ . '/bootstrap.php';

if (!empty($_POST)) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $price = $_POST['price'];
    $long = $_POST['long'];

    $accessToken = getToken();

    $apiClient->setAccessToken($accessToken)
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
        ->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                saveToken(
                    [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]
                );
            }
        );

    //Создадим контакт
    $contact = new ContactModel();
    $contact->setName($name);

    //Создадим коллекцию полей сущности
    $customFields = new CustomFieldsValuesCollection();

    // Добавим телефон
    $phoneField = new MultitextCustomFieldValuesModel();
    $phoneField->setFieldCode('PHONE');
    $phoneField->setValues(
        (new MultitextCustomFieldValueCollection())
            ->add(
                (new MultitextCustomFieldValueModel())
                ->setEnum('WORKDD')
                ->setValue($phone)
             )
    );
    $customFields->add($phoneField);

    // Добавим Email
    $phoneField = new MultitextCustomFieldValuesModel();
    $phoneField->setFieldCode('EMAIL');
    $phoneField->setValues(
        (new MultitextCustomFieldValueCollection())
            ->add(
                (new MultitextCustomFieldValueModel())
                ->setEnum('WORK')
                ->setValue($email)
             )
    );
    $customFields->add($phoneField);
    $contact->setCustomFieldsValues($customFields);

    try {
       $contactModel = $apiClient->contacts()->addOne($contact);
    } catch (AmoCRMApiException $e) {
       printError($e);
       die;
    }

    $leadsService = $apiClient->leads();

    //Создадим сделку с привязанными контактами и ценой
    $lead = new LeadModel();
    $lead->setName('Название сделки')
        ->setPrice($price)
        ->setContacts(
            (new ContactsCollection())
                ->add(
                    (new ContactModel())
                        ->setId($contact->id)
                )
        );

    if ($long) {
        $lead->setCustomFieldsValues(new \AmoCRM\Collections\CustomFieldsValuesCollection());

        $lead->getCustomFieldsValues()->add(\AmoCRM\Models\CustomFieldsValues\Factories\CustomFieldValuesModelFactory::createModel([
            'field_id' => 362451,
            'field_type' => \AmoCRM\Models\CustomFields\CustomFieldModel::TYPE_CHECKBOX,
            'values' => [['value' => 1]]
        ]));
    }

    $leadsCollection = new LeadsCollection();
    $leadsCollection->add($lead);

    try {
        $leadsCollection = $leadsService->add($leadsCollection);
    } catch (AmoCRMApiException $e) {
        printError($e);
        die;
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Новая заявка</title>
  </head>
  <body>
    <main>
        <section class="py-3">
            <div class="container">
                <div class="d-flex justify-content-center w-100">
                    <div class="col-md-6 col-xl-4">
                        <div class="rounded border p-3">
                            <h1>Новая заявка</h1>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" id="email">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="text" class="form-control" name="phone" id="phone">
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Цена</label>
                                <input type="text" class="form-control" name="price" id="price">
                            </div>
                            <input type="hidden" name="long" id="long">
                            <button type="submit" class="btn btn-primary">Отправить</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        window.onload = function(){
            setTimeout(function() {
                $("#long").val(1)
            }, 30000)
        };
    </script>
  </body>
</html>
