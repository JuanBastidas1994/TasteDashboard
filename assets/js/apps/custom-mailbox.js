$(document).ready(function() {


	var today = new Date();
  	var dd = String(today.getDate()).padStart(2, '0');
  	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
  	var yyyy = today.getFullYear();
  	var today = mm + '/' + dd + '/' + yyyy;

  	$('.current-recent-mail').text(today + ' -')


	// Applying Scroll Bar

	const ps = new PerfectScrollbar('.message-box-scroll');
	const mailScroll = new PerfectScrollbar('.mail-sidebar-scroll', {
		suppressScrollX : true
	});

	function mailInboxScroll() {
		$('.mailbox-inbox .collapse').each(function(){ const mailContainerScroll = new PerfectScrollbar($(this)[0], {
			suppressScrollX : true
		}); });
	}
	mailInboxScroll();

	// Open Modal on Compose Button Click
	$('#btn-compose-mail').on('click', function(event) {
		$('#btn-send').show();
		$('#btn-reply').hide();
		$('#btn-fwd').hide();
		$('#composeMailModal').modal('show');

		// Save And Reply Save
		$('#btn-save').show();
		$('#btn-reply-save').hide();
		$('#btn-fwd-save').hide();
	})

	/*
		Init. fn. checkAll ==> Checkbox check all
	*/
	document.getElementById('inboxAll').addEventListener('click', function() {
		var getActiveList = document.querySelectorAll('.tab-title .list-actions.active');
		var getActiveListID = '.'+getActiveList[0].id;

		var getItemsCheckboxes = '';

		if (getActiveList[0].id === 'BP' || getActiveList[0].id === 'E' || getActiveList[0].id === 'T' || getActiveList[0].id === 'O') {

			getItemsGroupCheckboxes = document.querySelectorAll(getActiveListID);
			for (var i = 0; i < getItemsGroupCheckboxes.length; i++) {
				getItemsGroupCheckboxes[i].parentNode.parentNode.parentNode;

				getItemsCheckboxes = document.querySelectorAll('.'+getItemsGroupCheckboxes[i].parentNode.parentNode.parentNode.className.split(' ')[0] + ' ' + getActiveListID + ' .inbox-chkbox');
				
				if (getItemsCheckboxes[i].checked) {
					getItemsCheckboxes[i].checked = false;
				} else {
					if (this.checked) {
						getItemsCheckboxes[i].checked = true;
					}
				}
			}

		} else {
			getItemsCheckboxes = document.querySelectorAll('.mail-item'+getActiveListID + ' .inbox-chkbox');
			for (var i = 0; i < getItemsCheckboxes.length; i++ ) {
				if (getItemsCheckboxes[i].checked) {
					getItemsCheckboxes[i].checked = false;
				} else {
					if (this.checked) {
						getItemsCheckboxes[i].checked = true;
					}
				}
			}
		}
	})

	/*
		fn. randomString ==> Generate Random Numbers
	*/
	function randomString(length, chars) {
		var result = '';
		for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
		return result;
	}

	/*
		fn. formatAMPM ==> Get Time in 24hr Format
	*/
	function formatAMPM(date) {
	  var hours = date.getHours();
	  var minutes = date.getMinutes();
	  var ampm = hours >= 12 ? 'PM' : 'AM';
	  hours = hours % 12;
	  hours = hours ? hours : 12; // the hour '0' should be '12'
	  minutes = minutes < 10 ? '0'+minutes : minutes;
	  var strTime = hours + ':' + minutes + ' ' + ampm;
	  return strTime;
	}

	/*
		fn. formatBytes ==> Calculate and convert bytes into ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
	*/
	function formatBytes(bytes, decimals) {
	    if (bytes === 0) return '0 Bytes';
	    const k = 1024;
	    const dm = decimals < 0 ? 0 : decimals;
	    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	    const i = Math.floor(Math.log(bytes) / Math.log(k));
	    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
	}

	// Search on each key pressed

	$('.input-search').on('keyup', function() {
	  var rex = new RegExp($(this).val(), 'i');
	    $('.message-box .mail-item').hide();
	    $('.message-box .mail-item').filter(function() {
	        return rex.test($(this).text());
	    }).show();
	});

	// Tooltip

	$('[data-toggle="tooltip"]').tooltip({
	    'template': '<div class="tooltip actions-btn-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
	})

	// Triggered when mail is Closed

	$('.close-message').on('click', function(event) {
		event.preventDefault();
		$('.content-box .collapse').collapse('hide')
		$(this).parents('.content-box').css({
			width: '0',
			left: 'auto',
			right: '-46px'
		});
	});

	// Open Mail Sidebar on resolution below or equal to 991px.

	$('.mail-menu').on('click', function(e){
		$(this).parents('.mail-box-container').children('.tab-title').addClass('mail-menu-show')
		$(this).parents('.mail-box-container').children('.mail-overlay').addClass('mail-overlay-show')
	})

	// Close sidebar when clicked on ovelay ( and ovelay itself ).

	$('.mail-overlay').on('click', function(e){
		$(this).parents('.mail-box-container').children('.tab-title').removeClass('mail-menu-show')
		$(this).removeClass('mail-overlay-show')
	})

	/*
		fn. contentBoxPosition ==> Triggered when clicked on any each mail to show the mail content.
	*/
	function contentBoxPosition() {
		$('.content-box .collapse').on('show.bs.collapse', function(event) {
			/*JC - CLICK EN LA ORDEN*/
			var getCollpaseElementId = this.id;
			var getSelectedMailTitleElement = $('.content-box').find('.mail-title');
			var getSelectedMailContentTitle = $(this).find('.mail-content').attr('data-mailTitle');
			$(this).parent('.content-box').css({
				width: '100%',
				left: '0',
				right: '100%'
			});
			$(this).parents('#mailbox-inbox').find('.message-box [data-target="#'+getCollpaseElementId+'"]').parents('.mail-item');
			getSelectedMailTitleElement.text(getSelectedMailContentTitle);
			getSelectedMailTitleElement.attr('data-selectedMailTitle', getSelectedMailContentTitle);
		})
	}
	function stopPropagations() {
		$('.mail-item-heading .mail-item-inner .new-control').on('click', function(e){
		    e.stopPropagation();
		})
	}


	$('#composeMailModal').on('hidden.bs.modal', function (e) {
	    
	  	$(this)
	    .find("input,textarea")
	       .val('')
	       .end();

	    for (var i = 0; i < $_getValidationField.length; i++) {
	      e.preventDefault();
	      $_getValidationField[i].style.display = 'none';
	    }
	})

	
	/*
		=========================
			Tab Functionality
		=========================
	*/
	var $listbtns = $('.list-actions').click(function() {
		$(this).parents('.mail-box-container').find('.mailbox-inbox > .content-box').css({
			width: '0',
			left: 'auto',
			right: '-46px'
		});
		$('.content-box .collapse').collapse('hide');
		var getActionCenterDivElement = $(this).parents('.mail-box-container').find('.action-center');
	  	if (this.id == 'mailInbox') {
			var $el = $('.' + this.id).show();
			getActionCenterDivElement.removeClass('tab-trash-active');
			$('#ct > div').not($el).hide();
	  	} else if (this.id == 'BP') {
	  		$el = '.' + $(this).attr('id');
	  		$elShow = $($el).show();
	  		getActionCenterDivElement.removeClass('tab-trash-active');
		    $('#ct > div .mail-item-heading'+$el).parents('.mail-item').show();
		    $('#ct > div .mail-item-heading').not($el).parents('.mail-item').hide();
	  	} else if (this.id == 'E') {
		    $el = '.' + $(this).attr('id');
	  		$elShow = $($el).show();
	  		getActionCenterDivElement.removeClass('tab-trash-active');
		    $('#ct > div .mail-item-heading'+$el).parents('.mail-item').show();
		    $('#ct > div .mail-item-heading').not($el).parents('.mail-item').hide();
	  	} else if (this.id == 'T') {
		    $el = '.' + $(this).attr('id');
	  		$elShow = $($el).show();
	  		getActionCenterDivElement.removeClass('tab-trash-active');
		    $('#ct > div .mail-item-heading'+$el).parents('.mail-item').show();
		    $('#ct > div .mail-item-heading').not($el).parents('.mail-item').hide();
	  	} else if (this.id == 'O') {
		    $el = '.' + $(this).attr('id');
	  		$elShow = $($el).show();
	  		getActionCenterDivElement.removeClass('tab-trash-active');
		    $('#ct > div .mail-item-heading'+$el).parents('.mail-item').show();
		    $('#ct > div .mail-item-heading').not($el).parents('.mail-item').hide();
	  		getActionCenterDivElement.removeClass('tab-trash-active');
	  	} else if (this.id == 'trashed') {
	  		var $el = $('.' + this.id).show();
	  		getActionCenterDivElement.addClass('tab-trash-active');
			$('#ct > div').not($el).hide();
	  	} else {
	    	var $el = $('.' + this.id).show();
	    	getActionCenterDivElement.removeClass('tab-trash-active');
	    	$('#ct > div').not($el).hide();
	  	}
	  	$listbtns.removeClass('active');
	  	$(this).addClass('active');
	})

	
	setTimeout(function() {
        $(".list-actions#ENTRANTE").trigger('click');
    },10);


	// Mark mail Priority/Groups as [ Personal, Work, Social, Private ]
	$(".label-group-item").on("click", function() {
		var getLabelColor = $(this).attr('class').split(' ')[1];
		var splitLabelColor = getLabelColor.split('-')[1];


		var notificationText = '';
		var getCheckedItemlength = $(".inbox-chkbox:checked").length;

		if ($(".inbox-chkbox:checked").parents('.mail-item-heading').hasClass(splitLabelColor)) {
			var notificationText = getCheckedItemlength < 2 ? getCheckedItemlength + ' Mail removed from '+ splitLabelColor.toUpperCase() +' Group' : getCheckedItemlength + ' Mails removed from '+ splitLabelColor.toUpperCase() +' Group';
		} else {
			var notificationText = getCheckedItemlength < 2 ? getCheckedItemlength + ' Mail Grouped as ' + splitLabelColor.toUpperCase() : getCheckedItemlength + ' Mails Grouped as ' + splitLabelColor.toUpperCase();
		}


	  	$(".inbox-chkbox:checked").parents('.mail-item-heading').toggleClass(splitLabelColor);
 		$(".inbox-chkbox:checked").prop('checked',false);
 		$("#inboxAll:checked").prop('checked',false);

 		Snackbar.show({
	        text: notificationText,
	        width: 'auto',
	        pos: 'top-center',
	        actionTextColor: '#bfc9d4',
	        backgroundColor: '#515365'
	    });
	});

	/*
		fn. $_sendMail ==> Trigger when clicked on Send Mail Button in Modal.
	*/

	
	contentBoxPosition();
	stopPropagations();

	$('.tab-title .nav-pills a.nav-link').on('click', function(event) {
	  $(this).parents('.mail-box-container').find('.tab-title').removeClass('mail-menu-show')
	  $(this).parents('.mail-box-container').find('.mail-overlay').removeClass('mail-overlay-show')
	})
	
});