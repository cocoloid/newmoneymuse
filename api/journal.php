<?php require_once __DIR__ . '/config.php';
// This file is the journal endpoint — save as journal.php
$user=$method=$db=null;
$user=requirePro();$method=$_SERVER['REQUEST_METHOD'];$db=getDB();
if($method==='GET'){$s=$db->prepare("SELECT * FROM journal_entries WHERE user_id=? ORDER BY created_at DESC");$s->execute([$user['id']]);respond($s->fetchAll());}
if($method==='POST'){$d=getBody();$type=$d['type']??'note';$content=trim($d['content']??'');if(!$content)respondError('Content required');$s=$db->prepare("INSERT INTO journal_entries(user_id,entry_type,content)VALUES(?,?,?)");$s->execute([$user['id'],$type,$content]);respond(['id'=>$db->lastInsertId(),'success'=>true],201);}
if($method==='DELETE'){$id=intval($_GET['id']??0);$db->prepare("DELETE FROM journal_entries WHERE id=? AND user_id=?")->execute([$id,$user['id']]);respond(['success'=>true]);}
respondError('Method not allowed',405);
