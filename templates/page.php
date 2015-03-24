<html>
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

</html>
