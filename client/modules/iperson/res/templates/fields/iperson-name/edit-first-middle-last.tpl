<div class="row">
    <div class="col-sm-3 col-xs-3">
        <select data-name="salutation{{ucName}}" class="form-control">
            {{options salutationOptions salutationValue field='salutationName' scope=scope}}
        </select>
    </div>
    <div class="col-sm-5 col-xs-5">
        <input type="text" class="form-control" data-name="initials{{ucName}}" value="{{initialsValue}}" placeholder="{{translate 'Initialen'}}"{{#if firstMaxLength}} maxlength="{{initialsMaxLength}}"{{/if}} autocomplete="espo-initials{{ucName}}">
    </div>
    <div class="col-sm-5 col-xs-5">
        <input type="text" class="form-control" data-name="first{{ucName}}" value="{{firstValue}}" placeholder="{{translate 'Roepnaam'}}"{{#if firstMaxLength}} maxlength="{{firstMaxLength}}"{{/if}} autocomplete="espo-first{{ucName}}">
    </div>
    <div class="col-sm-4 col-xs-4">
        <input type="text" class="form-control" data-name="middle{{ucName}}" value="{{middleValue}}" placeholder="{{translate 'Tussenvoegsel'}}"{{#if middleMaxLength}} maxlength="{{middleMaxLength}}"{{/if}} autocomplete="espo-middle{{ucName}}">
    </div>
    <div class="col-sm-12 col-xs-12">
        <input type="text" class="form-control" data-name="last{{ucName}}" value="{{lastValue}}" placeholder="{{translate 'Achternaam'}}"{{#if lastMaxLength}} maxlength="{{lastMaxLength}}"{{/if}} autocomplete="espo-last{{ucName}}">
    </div>
</div>
