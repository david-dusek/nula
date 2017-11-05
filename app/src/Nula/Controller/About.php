<?php

namespace Nula\Controller;

class About extends \Nula\Controller\Base {

  const EMAIL_SENT_STATUS_KEY = 'email-sent-status';
  const EMAIL_SENT_STATUS_OK = 'ok';
  const EMAIL_SENT_STATUS_ERROR = 'error';

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Exception
   */
  public function actionContact(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    $templateParameters = [
      'activeLink' => 'contact',
      'showMessageOk' => isset($args[self::EMAIL_SENT_STATUS_KEY]) && $args[self::EMAIL_SENT_STATUS_KEY] === self::EMAIL_SENT_STATUS_OK,
      'showMessageError' => isset($args[self::EMAIL_SENT_STATUS_KEY]) && $args[self::EMAIL_SENT_STATUS_KEY] === self::EMAIL_SENT_STATUS_ERROR,
    ];

    return $this->createTwigLocalizedResponse($request, $response, $args, 'about/contact.twig', $templateParameters);
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function actionContactEmail(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    $fullname = htmlspecialchars($request->getParam('fullname'));
    $emailBody = htmlspecialchars($request->getParam('email-body'));
    $emailFrom = $request->getParam('email-from');

    if (!isset($fullname) || !isset($emailBody) || !isset($emailFrom)) {
      $args[self::EMAIL_SENT_STATUS_KEY] = self::EMAIL_SENT_STATUS_ERROR;
    } else {
      try {
        $emailTo = 'info@plusminusnula.cz';
        $subject = "Zpráva z kontaktního formuláře od $fullname";
        $body = "Jméno: $fullname \n\nText e-mailu:\n$emailBody";
        $headers = "MIME-Version: 1.0\r\n"
          . "Content-type: text/plain; charset=UTF-8\r\n"
          . "From: \"$fullname\" <$emailFrom>\r\n"
          . "Reply-To: $emailFrom\r\n"
          . "Cc: $emailFrom\r\n";
        $args[self::EMAIL_SENT_STATUS_KEY] = \mail($emailTo, $subject, $body, $headers) ?
          $args[self::EMAIL_SENT_STATUS_KEY] = self::EMAIL_SENT_STATUS_OK :
          $args[self::EMAIL_SENT_STATUS_KEY] = self::EMAIL_SENT_STATUS_ERROR;
      } catch (\Exception $exception) {
        $args[self::EMAIL_SENT_STATUS_KEY] = self::EMAIL_SENT_STATUS_ERROR;
      }
    }

    return $response->withRedirect($this->router->pathFor('contact', $args));
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Exception
   */
  public function actionAtelier(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    return $this->createTwigLocalizedResponse($request, $response, $args, 'about/atelier.twig', ['activeLink' => 'atelier']);
  }

}