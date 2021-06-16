<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


class HomepagePresenter extends Nette\Application\UI\Presenter

{
    
	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database) {    
            
	$this->database = $database;
       
     } 
     
     public function renderDefault(): void {
         
         $this->template->members = $this->database->table('clen');
         $this->template->teams = $this->database->table('tym'); 
         
     }     
     
        public function createComponentInsertForm(): Form {    
            
         $form = new Form;
         
         $form->addText('jmeno', 'Jméno')
         ->setRequired();
         
         $form->addText('prijmeni', 'Přijmení')
         ->setRequired();
         
         $cislo_typu = $this->database->table('clen_typ')->select('id_typu, CONCAT(id_typu, "-" ,typ) id_typ')->fetchPairs('id_typu', 'id_typ');
         
         $form->addSelect('id_typu', 'Typ', $cislo_typu)
               ->setDefaultValue('1');
         
         $form->addSubmit('send', 'Vložit');
         
         $form->onSuccess[]= [$this, 'insertFormSucceded'];
         return $form;
         
        }
        
        public function insertFormSucceded(\stdClass $values): void {
            
           $id_clena = $this->getParameter('id_clena');
           
           $this->database->table('clen')->insert(
               [
                   'id_clena' => $id_clena,
                   'jmeno' => $values->jmeno,     
                   'prijmeni' => $values->prijmeni,
                   'id_typu' => $values->id_typu
               ]);
           
               $this->flashMessage('Úspěšně vloženo', 'success');
               $this->redirect('this');              
        }
        
        public function createComponentSecondInstertForm(): Form {
            $form = new Form;
            
            $form->addText('nazev', 'Název týmu')
            ->setRequired();
            
            $zavodnici = $this->database->table('clen')->select('id_clena, id_typu, CONCAT(jmeno, " ", prijmeni)')->where('id_typu = 1')->fetchAll();
            $spolujezdci = $this->database->table('clen')->select('id_clena, id_typu')->where('id_typu = 2')->fetchAll();
            $technici = $this->database->table('clen')->select('id_clena, id_typu')->where('id_typu = 3')->fetchAll();
            $manazeri = $this->database->table('clen')->select('id_clena, id_typu')->where('id_typu = 4')->fetchAll();
            $fotografove = $this->database->table('clen')->select('id_clena, id_typu')->where('id_typu = 5')->fetchAll();
            
            $form->addSelect('id_clena1', 'Závodníci č.:', $zavodnici)  
            ->setRequired();
            $form->addSelect('id_clena2', 'Spolujezdci č.:', $spolujezdci)
            ->setRequired();
            $form->addSelect('id_clena3', 'Technici č.:', $technici)
            ->setRequired();
            $form->addSelect('id_clena4', 'Manažeři č.:', $manazeri)
            ->setRequired();
            $form->addSelect('id_clena5', 'Fotografové č.:', $fotografove)
            ->setRequired();
            
            $form->addSubmit('send', 'Vložit');
            
            $form->onSuccess[]= [$this, 'secondInsertFormSucceded'];
            return $form;   
        }
        
        public function secondInsertFormSucceded(\stdClass $values): void {
            
           $id_tymu = $this->getParameter('id_tymu');
           
           $this->database->table('tym')->insert(
               [
                   'id_tymu' => $id_tymu,
                   'nazev' => $values->nazev,     
                   'id_clena1' => $values->id_clena1,
                   'id_clena2' => $values->id_clena2,
                   'id_clena3' => $values->id_clena3,
                   'id_clena4' => $values->id_clena4,
                   'id_clena5' => $values->id_clena5
               ]);
           
               $this->flashMessage('Úspěšně vloženo', 'success');
               $this->redirect('this');               
        }
        
}   
