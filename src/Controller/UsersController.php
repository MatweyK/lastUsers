<?php
namespace Drupal\users\Controller;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UsersController extends ControllerBase {


  /**
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  public function __construct( TimeInterface $time) {
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load the service required to construct this class.
      $container->get('datetime.time')
    );
  }

  function getUsers() {
    $build['#source_text'] = [];
    $request_time = $this->time->getCurrentTime();

    $ids = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->execute();
    $users = User::loadMultiple($ids);
    foreach($users as $user){
      $username = $user->get('name')->value;

      $creationTime = $user->get('created')->value;
      if (($request_time - $creationTime) <= 86400 ){
        $build['#source_text'][] = $username;
      }
    }

    $build['#theme'] = 'users';
    return $build;
  }
}
