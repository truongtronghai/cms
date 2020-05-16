<?php
namespace App\Controller;


class ArticlesController extends AppController{
  public function index(){
    $this->Authorization->skipAuthorization();
    $this->loadComponent('Paginator');
    $articles = $this->Paginator->paginate($this->Articles->find());
    //debug($this->Authentication->getResult());
    $this->set('articles',$articles);
  }

  public function view($slug = null){
    $article = $this->Articles->findBySlug($slug)->contain('Tags')->firstOrFail(); // Ham co dang Model::findBy.....()

    $this->Authorization->skipAuthorization();

    //debug($article);
    $this->set('article',$article);
  }

  public function edit($slug = null){

    if(is_null($slug)){
      $this->redirect(["action"=>"index"]);
    }

    $article = $this->Articles
                        ->findBySlug($slug)
                        ->contain('Tags') // *** can phai load associated model de load len mot entity co table lien ket voi no
                        ->firstOrFail();

    $this->Authorization->authorize($article,'update');

    if($this->request->is('put')){
      $article = $this->Articles->patchEntity($article,$this->request->getData(),[
        // disable modification of user_id
        'accessibleFields'=>['user_id'=>false]
      ]); // patch data lay duoc thanh dang entity article

      //debug($article);
      if($this->Articles->save($article)){
        $this->Flash->success(__("Article has been save successfully"));
        return $this->redirect(['action'=>'index']); // can return de thoat khoi action ngay lap tuc
      }
      $this->Flash->error(__("Saving article failed"));
    }else{
      // get list of Tags
      $tags = $this->Articles->Tags->find('list'); // thanks to Model of cakePHP, working with DB is cool

      $this->set('tags',$tags);
      $this->set('article',$article);
    }
  }

  public function add(){
    // tao mot obj trong cho article
    $article = $this->Articles->newEmptyEntity();

    $this->Authorization->authorize($article,'create');

    //debug($article);
    //debug($this->request->getData());
    if($this->request->is('post')){
      $article = $this->Articles->patchEntity($article,$this->request->getData()); // lay du lieu va dua ve dinh dang dung cua 1 entity article
      //Set the user_id from the current user
      $article->user_id = $this->request->getAttribute('identity')->getIdentifier(); // 'identity' chinh la user da login
      //debug($article);
      if($this->Articles->save($article)){
        $this->Flash->success(__("Article has been save successfully")); //  luu thong bao vao vung buffer dung chung voi template
        $this->redirect(['action'=>'index']);
      }else{
        $this->Flash->error(__("Saving article failed")); // khong can return vi no nam trong if(){}
      }
    }

    // get list of Tags
    $tags = $this->Articles->Tags->find('list'); // thanks to Model of cakePHP, working with DB is cool

    $this->set('tags',$tags);
    $this->set('article',$article);
  }

  public function delete($slug){
    $this->request->allowMethod(['post','delete']);
    $article = $this->Articles->findBySlug($slug)->firstOrFail();

    $this->Authorization->authorize($article);

    if($this->Articles->delete($article)){
      $this->Flash->success(__("The article {0} has been deleted!",$article->title));
      return $this->redirect(['action'=>'index']);
    }
  }

  public function tags(){
    $this->Authorization->skipAuthorization();

    $tags = $this->request->getParam('pass');
    //debug($tags);
    /*
    * 'tagged' la mot finder duoc tao ra o trong Table
    */

    $articles = $this->Articles->find('tagged', [
        'tags' => $tags
    ]);
    //debug($articles);
    $this->set([
      'articles' => $articles,
      'tags' => $tags
    ]);
  }
}
 ?>
