<?php

namespace Tornado\UserBundle\Services;
// use Tornado\UserBundle\Services\Oauth;

class Github extends Oauth
{

  public function handleResponse($response = FALSE)
  {
    if (empty($response)) {
      return FALSE;
    }

    parse_str($response, $output);

    return $output['access_token'];
  }

  public function getAccessToken($request)
  {
    if (!($code = $request->query->get('code'))) {
      return FALSE;
    }

    $github_auth = $this->get('client')->post(
      $this->get('auth_url'),
      null,
      array(
        'client_id' => $this->get('client_id'),
        'client_secret' => $this->get('client_secret'),
        'code' => $request->query->get('code'),
      )
    );

    $response = $github_auth->send();
    $access_token = $this->handleResponse($response->getBody(TRUE));

    $this->set('access_token', $access_token);
    return $access_token;
  }

  public function getUserData()
  {
    $github_user = $this->get('client')->get($this->get('user_url'));
    $github_user->setHeader('Authorization', "token $access_token");

    $response = $github_user->send();

    return $response->json();
  }

  public function getRepositories($access_token)
  {
    $github_user = $this->get('client')->get($this->get('user_url') . "/repos");
    $github_user->setHeader('Authorization', "token $access_token");

    $response = $github_user->send();

    return $response->json();
  }
}
