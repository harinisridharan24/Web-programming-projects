/*
** index.js - the js file that handles comment posting, deleting, tectcomplete to retreive user list
*/


 $(document).ready(function(){

   // the tectcomplete is 3rd party lbrary which helps in calling this function whenever user types in '@'
   $('.textarea').textcomplete([{ 
    match: /(^|\s)@(\w*)$/,
    search: function (term, callback) {
      // call search_users to retrieve user list matching the text entered
      $('#loader').show();
      $.ajax
        ({
          type: 'POST',
          dataType: 'json',
          url: 'search_users.php',
          data: 
          {
            searchTerm: term.trim()
          },
          success: function (response) 
          {
            $('#loader').hide();
            // a callback method to text complete to display matched users
            if(response.status == "success"){
              callback(response.users)
            }
          },
          error: function(error){
            $('#loader').hide();
            alert(error);
          }
        });
    },
    index: 2,
    // template on how to display the user list to the user and how to display once the user clicks 
    replace: function (data) {
      return data.fname+' '+data.lname;
    },

    template: function(data){
      return data.fname + ' ' + data.lname
    }
  }
], { maxCount: 20, debounce: 500 });


  // called when the user clicks the comment icon
  $('.comment-secton-toggle').on('click', function(e){
    $(this).parents('.photo-wrapper').find('.comment').val('');
    var photoId = $(this).parents('.photo-wrapper').find('.delete_photo_id').val()
    var $self = $(this);
    // makes call to server to retreive comments for the photo if the comment list is expanding
    if(!($self.parents('.photo-wrapper').find('.comments-container').is(':visible'))){
      $("#loader").show();
      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'get_comment.php',
        data: {photoId:photoId},
        success: function (response) 
        {
          $("#loader").hide();
          if(response.status == "success"){
            // create the html structure for each comment
            for (var i =0; i<response.comments.length;i++){
              var comment = response.comments[i]
              var deletedUser = comment.deleted_user;
              var commentHtml = '';
              var deleteHtml = ''
              var commentDateTime = getTimeDifference(comment.comment_date);

              if(comment.comment_user_id == response.current_user_id  || comment.fk_user_id == response.current_user_id){
                deleteHtml = '<i class="comment-delete fi-x medium"></i>';
              }

              // check if the comment is posted by a deleted user
              if(deletedUser == 0){
                commentHtml = '<div id="comment_'+comment.comment_id+'" class="comment-wrapper">'+
                '<input class="comment-id" type="hidden" value="'+comment.comment_id+'" />'+
                '<div class="comment-wrapper-left">'+
                  '<div class="comment-title">'+
                    '<a class="comment-author" href="index.php?id='+comment.comment_user_id+'"><img class="default-pic" src="default.png" width="50" height="50" />'+comment.username+'</a>'+
                    '<label class="comment-date">'+commentDateTime+'</label>'+
                  '</div>'+
                  '<div class="comment-text">'+
                    '<label>'+comment.comment_text+'</label>'+
                  '</div>'+
                '</div>'+
                '<div class="comment-wrapper-right">'+
                    deleteHtml+
                '</div>'+
                '</div>';
              }
              else {
                commentHtml = '<div id="comment_'+comment.comment_id+'" class="comment-wrapper">'+
                  '<input class="comment-id" type="hidden" value="'+comment.comment_id+'" />'+
                  '<div class="comment-wrapper-left">'+
                    '<div class="comment-title">'+
                      '<label class="comment-author deleted">[deleted]</label>'+
                      '<label class="comment-date">'+commentDateTime+'</label>'+
                    '</div>'+
                    '<div class="comment-text">'+
                      '<label>'+comment.comment_text+'</label>'+
                    '</div>'+
                  '</div>'+
                  '<div class="comment-wrapper-right">'+
                    deleteHtml+
                  '</div>'+
                  '</div>';
              }
              // append all the comments to the comments section
              $self.parents('.photo-wrapper').find('.comment-list').append(commentHtml);

            }
            $self.parents('.photo-wrapper').find('.comments-container').slideToggle('slow');   
          }
        },
        error: function(error){
           $('#loader').hide();
            alert(error);
        }
      });
    }
    else {
      // empty out the comments section
      $self.parents('.photo-wrapper').find('.comments-container').slideToggle('slow'); 
      $self.parents('.photo-wrapper').find('.comment-list').html(''); 
    }


 });


  // called when the user submits a comment
  $(".comment-form").on('submit', function(e) {

    var $form = $(this);
    var comment = $(this).find(".comment").val();
    var photoId = $(this).find(".photo_id").val();
    var username = $(this).find(".username").val();
    var currentDateTime = moment(new Date()).format('YYYY-MM-DD HH:mm:ss')
    // validate the comment length
    if(comment.trim().length > 0 && comment.trim().length <= 250 && photoId){
        comment = comment.trim();
        $(this).find('.comment-error').hide();
        $("#loader").show();
        $.ajax
        ({
          type: 'POST',
          dataType: 'json',
          url: 'post_comment.php',
          data: 
          {
            comment:comment,
            photoId:photoId,
            commentDate: currentDateTime
          },
          success: function (response) 
          {
            // create a new comment block to be displayed in the comment section and append it as the first item in the list
                $("#loader").hide();
            if(response.status == "success"){
              $form.find(".comment").val(""); 
              var commentCount = parseInt($form.parents('.photo-wrapper').find('.count').text()); 
              $form.parents('.photo-wrapper').find('.count').text(parseInt(commentCount+1));
              $form.parents('.photo-wrapper').find('.comment-list').prepend('<div id="comment_'+response.id+'" class="comment-wrapper"><input class="comment-id" type="hidden" value="'+response.id+'"/><div class="comment-wrapper-left"><div class="comment-title"><a class="comment-author" href="index.php?id='+response.userId+'"><img class="default-pic" src="default.png" width="50" height="50" />'+username+'</a><label class="comment-date">Just Now</label></div><div class="comment-text"><label>'+comment+'</label></div></div><div class="comment-wrapper-right"><i class="comment-delete fi-x medium"></i></div></div>').hide().show('slow');
            }
          },
          error: function(error){
            $('#loader').hide();
            alert(error);
          }
        });
      }
      else {
        // display releveant validation error messages
        if(comment.trim().length<=0){
          $(this).find('.comment-error').text('Please type in a comment before submitting.')
        }
        else if(comment.trim().length>250){
          $(this).find('.comment-error').text('Comments cannot exceed 250 characters')
        }
        $(this).find('.comment-error').show();
      }
      return false;
  });

  // called when user clicks the delete photo link and displays a confirm delete modal
  $('.photo-delete').on('click', function(){
    var photoWrapper = $(this).parents('.photo-wrapper');
    var photoId = $(photoWrapper).find(".photo_id").val();
    $('#delete-photo-modal').foundation('open');
    $('#delete-photo-modal').find("#delete-photo-id").val(photoId);
  });

  // called when user confirms photo deletion
  $("#delete-photo").on('click', function(){
    var photoId = $("#delete-photo-id").val(); 
    $.ajax
      ({
        type: 'POST',
        dataType: 'json',
        url: 'delete_photo.php',
        data: 
        {
          photoId:photoId
        },
        success: function (response) 
        {
          // on successful deletion photo block is removed from the photo list and modal is closed
          if(response.status == "success"){
            $('#photo_'+photoId).fadeOut().remove();
            if(!$('.photo-wrapper').length ){
              $('#photo-container').append("<div id='empty-photos' class='callout warning'>Oopsie. There are no photos</div>");
            }
            $("#delete-photo-id").val(''); 
            $('#delete-photo-modal').foundation('close');  
          }
        },
        error: function(error){
           $('#loader').hide();
          alert(error);
        }
      });

  });
// called when user clicks cancel in the modal
$('#cancel-photo').on('click', function(){
  $('#delete-photo-modal').foundation('close');          
});

// called when user clicks the delete comment link and displays a confirm delete modal
$(document).delegate(".comment-delete","click",function(e){
  var commentWrapper = $(this).parents('.comment-wrapper');
  var commentId = $(commentWrapper).find(".comment-id").val();
  $('#delete-comment-modal').foundation('open');
  $('#delete-comment-modal').find("#delete-comment-id").val(commentId);
});

$("#delete-comment").on('click', function(){
    var commentId = $("#delete-comment-id").val();  
    $.ajax
    ({
      type: 'POST',
      dataType: 'json',
      url: 'delete_comment.php',
      data: 
      {
        commentId:commentId
      },
      success: function (response) 
      {
        // on successful deletion comment block is removed from the comment list and modal is closed        
        if(response.status == "success"){
          var commentCount = parseInt($('#comment_'+commentId).parents('.photo-wrapper').find('.count').text()); 
          $('#comment_'+commentId).parents('.photo-wrapper').find('.count').text(parseInt(commentCount-1));
          $('#comment_'+commentId).fadeOut().remove();
          $("#delete-comment-id").val('');
          $('#delete-comment-modal').foundation('close');          
        }
      },
      error: function(error){
         $('#loader').hide();
          alert(error);
      }
    });
})
// called when user clicks cancel in the modal
$('#cancel-comment').on('click', function(){
  $('#delete-comment-modal').foundation('close');          
})


    
// called when user clicks the delete account link and displays a confirm delete modal
$('#manage-account').on('click', function(e){
  $('#delete-account-modal').foundation('open');
});

 $('#delete-account').on('click', function(e){ 
    $.ajax
      ({
        type: 'POST',
        dataType: 'json',
        url: 'delete_account.php',
        data: {},
        success: function (response) 
        {
          // on successful deletion user is redirrected to logout page        
          if(response.status == "success"){
            window.location.href = 'logout.php';
          }
        },
        error: function(error){
           $('#loader').hide();
          alert(error);
        }
      });


  });
// called when user clicks cancel in the modal
$('#cancel-delete').on('click', function(){
  $('#delete-account-modal').foundation('close');          
})


});

// js method to calculate the time difference for the comment being posted
function getTimeDifference(commentDateTime){
    var day = 60*24
    var days = 60*24*2
    var week = 60*24*7
    var weeks = 60*24*7*2

    if(commentDateTime !== null){
      var timeDifference = (moment(new Date()).diff(commentDateTime))
      var timeDifferenceInMinutes = Math.floor(moment.duration(timeDifference).asMinutes())

      if(timeDifferenceInMinutes >= 0 && timeDifferenceInMinutes < 1) {
        return 'just now'
      }
       else if(timeDifferenceInMinutes >= 1 && timeDifferenceInMinutes < 2) {
        return 'a minute ago'
      }

      else if(timeDifferenceInMinutes >= 2 && timeDifferenceInMinutes < 60) {
        return timeDifferenceInMinutes + ' minutes ago'
      }
      else if(timeDifferenceInMinutes >= 60 && timeDifferenceInMinutes < 120) {
        return '1 hour ago'
      }
      else if(timeDifferenceInMinutes >= 120 && timeDifferenceInMinutes <day) {
        var timeDifferenceInHours = Math.floor(moment.duration(timeDifference).asHours())
        return timeDifferenceInHours + ' hours ago'
      }

      else if(timeDifferenceInMinutes >= day && timeDifferenceInMinutes < days) {
        return '1 day ago'
      }
      else {
        return moment(commentDateTime).format('MMM D, YYYY')
      }
    }
}