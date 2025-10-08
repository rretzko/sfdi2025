<?php

namespace App\Http\Controllers\Square;

//require 'vendor/autoload.php';

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Square\SquareClientBuilder;
use Square\Authentication\BearerAuthCredentialsBuilder;
use Square\Environment;
use Square\Exceptions\ApiException;

class QuickstartController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $client = SquareClientBuilder::init()
            ->bearerAuthCredentials(
                BearerAuthCredentialsBuilder::init(
                    config('app.squareSandboxAccessToken')
                )
            )
            ->environment(Environment::SANDBOX)
            ->build();

        try {

            $apiResponse = $client->getLocationsApi()->listLocations();

            if ($apiResponse->isSuccess()) {
                $result = $apiResponse->getResult();
                foreach ($result->getLocations() as $location) {
                    printf(
                        "%s: %s, %s, %s<p/>",
                        $location->getId(),
                        $location->getName(),
                        $location->getAddress()->getAddressLine1(),
                        $location->getAddress()->getLocality()
                    );
                }

            } else {
                $errors = $apiResponse->getErrors();
                foreach ($errors as $error) {
                    printf(
                        "%s<br/> %s<br/> %s<p/>",
                        $error->getCategory(),
                        $error->getCode(),
                        $error->getDetail()
                    );
                }
            }

        } catch (ApiException $e) {
            echo "ApiException occurred: <b/>";
            echo $e->getMessage()."<p/>";
        }

    }
}
