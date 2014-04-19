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

  public function getUserData($access_token)
  {
    // $access_token = $this->get('access_token');

    $user_data = $this->get('client')->get($this->get('user_url'));
    $user_data->setHeader('Authorization', "token $access_token");

    $response = $user_data->send();

    return $response->json();
  }

  public function getRepositories($access_token)
  {
    // $access_token = $this->get('access_token');

    $user_repos = $this->get('client')->get($this->get('user_url') . "/repos");
    $user_repos->setHeader('Authorization', "token $access_token");

    $response = $user_repos->send();

    return $response->json();
  }
}
