<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title>ﾓﾊﾞｲﾙ掲示板</title>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td bgcolor="#9999FF">ﾓﾊﾞｲﾙ掲示板</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td bgcolor="#EEEEEE">内容の確認</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td bgcolor="#FFCCFF"><?= validation_errors(); ?></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td bgcolor="#EEEEEE">名前<br><?= html_escape($form['name']); ?></td>
</tr>
<tr>
<td>ﾒｰﾙｱﾄﾞﾚｽ<br><?php if ($form['email'] == '') { echo '(なし)'; } else { echo html_escape($form['email']); } ?></td>
</tr>
<tr>
<td bgcolor="#EEEEEE">件名<br><?= html_escape($form['subject']); ?></td>
</tr>
<tr>
<td>内容<br><?= nl2br(html_escape($form['body'])); ?></td>
</tr>
<tr>
<td bgcolor="#EEEEEE">削除ﾊﾟｽﾜｰﾄﾞ<br><?php if ($form['password'] == '') { echo '(なし)'; } else { echo html_escape($form['password']); } ?></td>
</tr>
<tr>
<td>
<?= form_open('bbs/post', ['accept-charset' => 'Shift_JIS']); ?>
<?= form_hidden('name',     $form['name']); ?>
<?= form_hidden('email',    $form['email']); ?>
<?= form_hidden('subject',  $form['subject']); ?>
<?= form_hidden('body',     $form['body']); ?>
<?= form_hidden('password', $form['password']); ?>
<input type="submit" value="修正する" />
<?= form_close(); ?>
<br>
<?= form_open('bbs/insert', ['accept-charset' => 'Shift_JIS']); ?>
<?= form_hidden('name',     $form['name']); ?>
<?= form_hidden('email',    $form['email']); ?>
<?= form_hidden('subject',  $form['subject']); ?>
<?= form_hidden('body',     $form['body']); ?>
<?= form_hidden('password', $form['password']); ?>
<?= form_hidden('key',      $form['key']); ?>
<?= form_hidden('captcha',  $form['captcha']); ?>
<input type="submit" value="送信する" />
<?= form_close(); ?>
</td>
</tr>
</table>

<hr>
<?= anchor('bbs', 'ﾄｯﾌﾟに戻る'); ?>

</body>
</html>
