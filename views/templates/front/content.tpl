<p>
Vlucht van {$origin} naar {$destination}
</p>

{if $foundSchedule}
{include file="$bookflighttickets_tpl_dir/found.tpl"}
{else} 
Helaas, er zijn geen resultaten gevonden.
{/if}
