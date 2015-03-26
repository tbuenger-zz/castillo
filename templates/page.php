<?= snippet('header') ?>
<script src="/assets/main.js"></script>
<h1><?= $page->title() ?></h1>
<p>Diese Seite wurde erstellt von <?= $page->creator() ?> 
am <?= $page->created()->format('d.m.Y') ?>.</p>

<h2>Performances: <?php echo $page->performances()->count() ?></h2>
<ul>
    <?php foreach($page->performances()->sortBy('date') as $performance):?>
        <li>
            <div><?= $performance->role() ?></div>
            <div><?= $performance->house() ?></div>
            <div><?= $performance->place() ?></div>
            <div><?= $performance->date()->format('d.m.Y') ?></div>
        </li>
    <?php endforeach ?>
</ul>

<h2>Files: <?php echo $page->files()->count() ?></h2>
<ul>
    <?php foreach($page->files() as $img):?>
        <li>
            <div><?= $img->url() ?></div>
            <div><?= $img->name() ?></div>
            <div><?= $img->title() ?></div>
        </li>
    <?php endforeach ?>
</ul>

<h2>Pages: <?php echo $page->pages()->count() ?></h2>
<ul>
    <?php foreach($page->pages() as $p):?>
        <li>
            <div><a href="<?= $p->url() ?>"><?= $p->title() ?></a></div>
        </li>
    <?php endforeach ?>
</ul>

<h1>Favourite file: <?= $page->file('page')->url() ?></h1>

</html>
