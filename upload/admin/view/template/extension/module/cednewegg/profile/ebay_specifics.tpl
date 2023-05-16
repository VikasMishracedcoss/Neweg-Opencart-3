<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_listing_type; ?></label>
    <div class="col-sm-10">
        <select name="profile_specifics[listing_type]"   class="form-control">
            <?php if($listing_type && count($listing_type)) {
            foreach ($listing_type as $type) { ?>
            <option value="<?php echo $type['value'] ?>"
            <?php if(isset($profile_specifics['listing_type']) && $profile_specifics['listing_type'] == $type['value']){ ?>
            selected="selected"
            <?php } ?>
            >
            <?php echo $type['label']; ?></option>
            <?php } ?>     <?php } ?>
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_item_condition; ?></label>
    <div class="col-sm-10">
        <select name="profile_specifics[item_condition]"   class="form-control">
            <?php if($item_conditions && count($item_conditions)) {
            foreach ($item_conditions as $condition) { ?>
            <option value="<?php echo $condition['ID'] ?>"
            <?php if(isset($profile_specifics['item_condition']) && $profile_specifics['item_condition'] == $condition['ID']){ ?>
            selected="selected"
            <?php } ?>
            >
            <?php echo $condition['DisplayName']; ?></option>
            <?php } ?>     <?php } ?>
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_listing_duration; ?></label>
    <div class="col-sm-10">
        <select name="profile_specifics[listing_duration]"   class="form-control">
            <?php if($listing_durations && count($listing_durations)) {
            foreach ($listing_durations as $duration) { ?>
            <option value="<?php echo $duration['value'] ?>"
            <?php if(isset($profile_specifics['listing_duration']) && $profile_specifics['listing_duration'] == $duration['value']){ ?>
            selected="selected"
            <?php } ?>
            >
            <?php echo $duration['label']; ?></option>
            <?php } ?>     <?php } ?>
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_item_location; ?></label>
    <div class="col-sm-10">
        <input type="text" name="profile_specifics[item_location]" id="" value="<?php if(isset($profile_specifics['item_location'])){ echo $profile_specifics['item_location']; }?>"
               class="form-control"/>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_postal_code; ?></label>
    <div class="col-sm-10">
        <input type="text" name="profile_specifics[postal_code]" id="" value="<?php if(isset($profile_specifics['postal_code'])){ echo $profile_specifics['postal_code']; }?>"
               class="form-control"/>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_max_dispatch_time; ?></label>
    <div class="col-sm-10">
        <input type="text" name="profile_specifics[max_dispatch_time]" id="" value="<?php if(isset($profile_specifics['max_dispatch_time'])){ echo $profile_specifics['max_dispatch_time']; } ?>"
               class="form-control"/>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_max_quantity; ?></label>
    <div class="col-sm-10">
        <input type="number" name="profile_specifics[max_quantity]" id="" value="<?php if(isset($profile_specifics['max_quantity'])){ echo $profile_specifics['max_quantity'];} ?>"
               class="form-control"/>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 control-label"
           for="input-variation_type"><?php echo $label_price_type; ?></label>
    <div class="col-sm-10">
        <select name="profile_specifics[price_variation_type]" onchange="changePriceVariation()" id="price_variation_type" class="form-control">
            <option value="default" selected="selected">-None-</option>
            <option value="increase_fixed"
            <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'increase_fixed'){ ?>
            selected="selected"
            <?php } ?>
            ><?php echo $label_increase_fixed; ?></option>
            <option value="increase_percentage"
            <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'increase_percentage'){ ?>
            selected="selected"
            <?php } ?>
            ><?php echo $label_increase_percentage; ?></option>
            <option value="decrease_fixed"
            <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'decrease_fixed'){ ?>
            selected="selected"
            <?php } ?>
            ><?php echo $label_decrease_fixed; ?></option>
            <option value="decrease_percentage"
            <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'decrease_percentage'){ ?>
            selected="selected"
            <?php } ?>
            ><?php echo $label_decrease_percentage; ?></option>
        </select>
    </div>
</div>
<div class="form-group row" id="price_variation_value_section">
    <label class="col-sm-2 control-label"
           for="input-title"><?php echo $label_price_variation_value; ?></label>
    <div class="col-sm-10">
        <input type="number" name="profile_specifics[price_variation_value]" id="price_variation_value" value="<?php if(isset($profile_specifics['price_variation_value'])){ echo $profile_specifics['price_variation_value'];} ?>"
               class="form-control"/>
    </div>
</div>

<script>

    function numberValidate(event, el) {
        var attr = el.placeholder;
        const charCode = (event.which) ? event.which : event.keyCode;
        if (charCode !== 46 && charCode!== 8 && charCode!==8 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }
</script>