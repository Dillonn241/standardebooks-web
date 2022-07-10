<?
require_once('Core.php');

$ebooks = [];

try{
	$urlPath = trim(str_replace('.', '', HttpInput::Str(GET, 'url-path', true) ?? ''), '/'); // Contains the portion of the URL (without query string) that comes after https://standardebooks.org/ebooks/
	$wwwFilesystemPath = EBOOKS_DIST_PATH . $urlPath; // Path to the deployed WWW files for this ebook

	if($urlPath == '' || mb_stripos($wwwFilesystemPath, EBOOKS_DIST_PATH) !== 0 || !is_dir($wwwFilesystemPath)){
		// Ensure the path exists and that the root is in our www directory
		throw new Exceptions\InvalidAuthorException();
	}

	$ebooks = Library::GetEbooksByAuthor($wwwFilesystemPath);

	if(sizeof($ebooks) == 0){
		throw new Exceptions\InvalidAuthorException();
	}
}
catch(Exceptions\InvalidAuthorException $ex){
	Template::Emit404();
}
?><?= Template::Header(['title' => 'Ebooks by ' . strip_tags($ebooks[0]->AuthorsHtml), 'highlight' => 'ebooks', 'description' => 'All of the Standard Ebooks ebooks by ' . strip_tags($ebooks[0]->AuthorsHtml)]) ?>
<main class="ebooks">
	<h1<? if(sizeof($ebooks) > 1){ ?> class="is-collection"<? } ?>>Ebooks by <?= $ebooks[0]->AuthorsHtml ?></h1>
	<? if(sizeof($ebooks) > 1){ ?>
		<p class="download-collection"><a  href="<?= Formatter::ToPlainText($ebooks[0]->AuthorsUrl) ?>/downloads">Download all ebooks in this collection</a></p>
	<? } ?>
	<?= Template::EbookGrid(['ebooks' => $ebooks, 'view' => VIEW_GRID]) ?>
	<p class="feeds-alert">We also have <a href="/bulk-downloads">bulk ebook downloads</a> available, as well as <a href="/feeds">ebook catalog feeds</a> for use directly in your ereader app or RSS reader.</p>
	<?= Template::ContributeAlert() ?>
</main>
<?= Template::Footer() ?>
