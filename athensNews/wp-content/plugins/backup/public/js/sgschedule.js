jQuery(document).ready( function() {
    sgBackup.initTablePagination();
    sgBackup.initScheduleCreation();
});

sgBackup.initScheduleCreation = function() {
    sgBackup.initScheduleSwitchButtons();
    sgBackup.initManulBackupRadioInputs();
    sgBackup.initManualBackupTooltips();
    sgBackup.toggleDaySelection();
    sgBackup.initIntervalSelection();
}

sgBackup.removeSchedule = function (id){
    var ajaxHandler = new sgRequestHandler('schedule', {remove: true, id: id});

    if (!confirm('Are you sure?')) {
        return false;
    }

    ajaxHandler.callback = function(response){
        jQuery('.alert').remove();

        if(typeof response.success !== 'undefined'){
            location.reload();
        }
        else{
            //if error
            var alert = sgBackup.alertGenerator('Unable to delete schedule', 'alert-danger');
            jQuery('.sg-schedule-container legend').after(alert);
        }

        sgBackup.scrollToElement('.alert');
    };
    ajaxHandler.run();
}

sgBackup.initIntervalSelection = function(){
    if (jQuery('#sg-schedule-interval').val() == '* * 0') {
        jQuery('#sg-schedule-day-of-week-select').show();
    }
    else if(jQuery('#sg-schedule-interval').val() == '1 * *') {
        jQuery('#sg-schedule-day-of-month-select').show();
    }

    jQuery('#sg-schedule-interval').on('change', function(){
        if (jQuery(this).val() == '* * 0') {
            jQuery('#sg-schedule-day-of-month-select').hide();
            jQuery('#sg-schedule-day-of-week-select').show();
        }
        else if (jQuery(this).val() == '1 * *') {
            jQuery('#sg-schedule-day-of-week-select').hide();
            jQuery('#sg-schedule-day-of-month-select').show();
        }
        else {
            sgBackup.toggleDaySelection();
        }
    });
}

sgBackup.toggleDaySelection = function(){
    jQuery('#sg-schedule-day-of-week-select').hide();
    jQuery('#sg-schedule-day-of-month-select').hide();
}

sgBackup.prependErrorMsg = function(alert) {
    if (typeof jQuery('#sg-schedule-id').val() === 'undefined') {
        jQuery('.sg-schedule-container legend').after(alert);
    }
    else {
        jQuery('#sg-modal .modal-header').prepend(alert);
    }
}

//SGSchedule AJAX callback
sgBackup.schedule = function(){
    var error = [];
    var scheduleForm = jQuery('form[data-type=schedule]');

    //Validation
    jQuery('.alert').remove();
    if(jQuery('input[type=radio][name=backupType]:checked').val() == 2) {
        if (jQuery('.sg-custom-option:checked').length <= 0) {
            error.push('Please choose at least one option.');
        }
        //Check if any file is selected
        if (jQuery('input[type=checkbox][name=backupFiles]:checked').length > 0) {
            if (jQuery('.sg-custom-backup-files input:checkbox:checked').length <= 0) {
                error.push('Please choose at least one directory.');
            }
        }
    }
    //Check if any cloud is selected
    if(jQuery('input[type=checkbox][name=backupCloud]:checked').length > 0) {
        if(jQuery('.sg-custom-backup-cloud input:checkbox:checked').length <= 0) {
            error.push('Please choose at least one cloud.');
        }
    }
    //If any error show it and abort ajax
    if(error.length){
        var alert = sgBackup.alertGenerator(error, 'alert-danger');
        sgBackup.prependErrorMsg(alert);
        sgBackup.scrollToElement('.alert');
        return false;
    }

    //Before sending
    jQuery('#sg-save-schedule').attr('disabled','disabled');
    jQuery('#sg-save-schedule').html('Saving...');

    //On Success
    var ajaxHandler = new sgRequestHandler('schedule', scheduleForm.serialize());
    ajaxHandler.dataIsObject = false;
    ajaxHandler.callback = function(response){
        jQuery('.alert').remove();
        if(typeof response.success !== 'undefined'){
            var alert = sgBackup.alertGenerator('You have successfully activated schedule.', 'alert-success');
            sgBackup.prependErrorMsg(alert);
            location.reload();
        }
        else{
            //if error
            var alert = sgBackup.alertGenerator(response, 'alert-danger');
            sgBackup.prependErrorMsg(alert);
        }

        //Always
        jQuery('#sg-save-schedule').removeAttr('disabled','disabled');
        jQuery('#sg-save-schedule').html('Save');
        sgBackup.scrollToElement('.alert');
    };
    ajaxHandler.run();
};

sgBackup.initScheduleSwitchButtons = function() {
    jQuery('.sg-switch').bootstrapSwitch();
    if(jQuery('.sg-switch').is(':checked'))
    {
        jQuery('.sg-schedule-settings').show();
    }
    jQuery('.sg-switch').on('switchChange.bootstrapSwitch', function (event, state) {
        var url = jQuery(this).attr('data-remote');
        if(state) {
            var isFeatureAvailable = new sgRequestHandler('isFeatureAvailable', {sgFeature: "SCHEDULE"});
            isFeatureAvailable.callback = function(response) {
                jQuery('.alert').remove();
                if (typeof response.success !== 'undefined') {
                    //Show or Hide settings panel
                    jQuery('.sg-schedule-settings').fadeIn();
                }
                else {
                    var alert = sgBackup.alertGenerator(response.error, 'alert-warning');
                    jQuery('.sg-schedule-container legend').after(alert);
                    jQuery('.sg-switch').bootstrapSwitch('state', false);
                }
            }

            isFeatureAvailable.run();
        }
        else {
            var ajaxHandler = new sgRequestHandler('schedule', {remove: true});
            ajaxHandler.run();
            jQuery('.sg-schedule-settings').fadeOut();
        }
    });
};

sgBackup.initManulBackupRadioInputs = function(){
    jQuery('input[type=radio][name=backupType]').off('change').on('change', function(){
        jQuery('.sg-custom-backup').fadeToggle();
        jQuery('.sg-custom-backup').children().find('input[class^=sg-custom]').removeAttr('checked');
        jQuery('.sg-custom-backup-files').hide();
    });
    jQuery('input[type=checkbox][name=backupFiles], input[type=checkbox][name=backupCloud], input[type=checkbox][name=backupDatabase]').off('change').on('change', function(){
        var sgCheckBoxWrapper = jQuery(this).closest('.checkbox').find('.sg-checkbox');
        sgCheckBoxWrapper.fadeToggle();
        if(jQuery(this).attr('name') == 'backupFiles') {
            sgCheckBoxWrapper.find('input[type=checkbox]').attr('checked', 'checked');
        }
    });
	jQuery('input[type=radio][name=backupDBType]').off('change').on('change',function(){
		var sgCheckBoxWrapper = jQuery(this).closest('.checkbox').find('.sg-custom-backup-tables');
		if(jQuery('input[type=radio][name=backupDBType]:checked').val() == '2'){
			sgCheckBoxWrapper.find('input[type=checkbox]').not("[disabled]").prop('checked', true)
			sgCheckBoxWrapper.fadeIn();
		}else{
			sgCheckBoxWrapper.fadeOut();
			sgCheckBoxWrapper.find('input[type=checkbox][current="true"]').not("[disabled]").prop('checked', true)
			sgCheckBoxWrapper.find('input[type=checkbox][current="false"]').prop('checked', false)
		}
	})
}

sgBackup.initManualBackupTooltips = function(){
    jQuery('[for=cloud-ftp]').tooltip();
    jQuery('[for=cloud-dropbox]').tooltip();
    jQuery('[for=cloud-gdrive]').tooltip();
    jQuery('[for=cloud-one-drive]').tooltip();
    jQuery('[for=cloud-amazon]').tooltip();
}
