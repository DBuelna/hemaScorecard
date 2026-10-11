<div class='cell ' style='border-bottom:2px solid black'>
    <div class='grid-x grid-margin-x'>
        <div class='large-4 medium-5 cell align-self-middle'>
            <img style='width: 400px;' src="includes/images/logo_rect.jpg">
        </div>
        <div class='large-8 medium-7 cell align-self-middle'>
            <p>HEMA Scorecard is a <b>FREE</b> online tournament management software for Historical European Martial Arts tournaments. If you are interested in using HEMA Scorecard to hold a tournament of your own, <a href='infoWhy.php'> why not have a look at some of it's best features</a>? </p>
        </div>
    </div>
</div>

<BR>

<h3>Recent and Upcoming Events (<a href='infoSelect.php'>Full Event List</a>)</h3>

<!-- Tabs ------------------------------------------------------------>
<ul class="tabs" data-tabs id="recent-events-tabs">
    <li class="tabs-title <?=$activeClass?>">
        <a data-tabs-target="panel-active">Active</a></li>
    <li class="tabs-title"><a data-tabs-target="panel-upcoming">
        Upcoming</a></li>
    <li class="tabs-title <?=$recentClass?>"><a data-tabs-target="panel-recent">
        Recent</a></li>
</ul>

<!-- Panels ------------------------------------------------------------>
<div class="tabs-content" data-tabs-content="recent-events-tabs">
    <div class="tabs-panel <?=$activeClass?>" id="panel-active">
        <?=displayEventTabe($eventsToShow['active'])?>
    </div>

    <div class="tabs-panel" id="panel-upcoming">
        <?=displayEventTabe($eventsToShow['upcoming'])?>
    </div>

    <div class="tabs-panel <?=$recentClass?>" id="panel-recent">
        <?=displayEventTabe($eventsToShow['recent'])?>
    </div>

</div>

