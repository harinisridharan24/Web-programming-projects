# Tables Example For Photo Upload Site
#
# D Provine


CREATE TABLE `users` (
  `user_id` int(6) NOT NULL auto_increment,
  `fname` varchar(15),
  `lname` varchar(15),
  `username` varchar(40),
  `password` char(40), 
  PRIMARY KEY  (`user_id`)
);

# If you want to use SQL constraints for extra error-checking, see
# http://elvis.rowan.edu/~kilroy/awp/Wk8.2-SQL2/BetterKeys.txt


# When adding users, the command will look like this:
#
# insert into photo_users
#    values(default, "2013-08-08", "bob", "3da541559918a808c2402bba5012f6c60b27661c", "");
#
# where the password field, "3da541559918a808c2402bba5012f6c60b27661c",
# is the result of the PHP "sha1" function.  (There are other choices,
# but be sure you use the same one to both set and check a password.)
#
#  $newuser_query = 
#    insert into photo_users values(default, :date, :name, :pword, "");
#
# and then use the sha1() function when you call bindParam() to set the
# variables, like this:
#
#        $pword = sha1($_POST['pword']);
#
# This will save the password to the database encrypted, instead of plain
# text.  When someone logs in, your select will have to have a WHERE clause
# something like:
#
#   '.... WHERE username=:name AND password=:pword)'
#
# but you'll have to sha1() the entered password when calling
# bindParam().


CREATE TABLE `photos` (
  `photo_id` int(8) NOT NULL auto_increment,
  `uploaddate` datetime,
  `uploadname` varchar(128),
  `uploadlocation` varchar(256),
  `caption` varchar(256),      
  `fk_user_id` int(6),
  PRIMARY KEY  (`photo_id`),
  FOREIGN KEY (`fk_user_id`) REFERENCES users(`user_id`) ON DELETE CASCADE
);

# Note that these two tables do NOT specify foreign key constraints;
# if you want to add that, see:
# http://elvis.rowan.edu/~kilroy/awp/Wk8.2-SQL2/BetterKeys.txt
#
# You probably want "on delete cascade", so if an account is
# deleted all the associated picture are deleted, and all the
# comments on those pictures are deleted.  Test carefully!


CREATE TABLE `comments` (
  `comment_id` int(8) NOT NULL auto_increment,
  `comment_user_id` int(6), # user who LEFT the comment!
  `fk_photo_id` int(8),
  `comment_text` varchar(128),
  `username` varchar(256),
  `comment_date` datetime,
  PRIMARY KEY  (`comment_id`),
  FOREIGN KEY (`fk_photo_id`) REFERENCES photos(`photo_id`) ON DELETE CASCADE
);

CREATE TABLE `tags` (
  `tag_id` int(8) NOT NULL auto_increment,
  `fk_photo_id` int(8),
  `tag_text` varchar(256),
  PRIMARY KEY  (`tag_id`),
  FOREIGN KEY (`fk_photo_id`) REFERENCES photos(`photo_id`) ON DELETE CASCADE
);

CREATE TABLE `confirm` (
  `confirm_id` int(11) NOT NULL auto_increment,
  `userid` varchar(128),
  `confirmationkey` varchar(250),
  `useremail` varchar(40),
  PRIMARY KEY  (`confirm_id`)
)

CREATE TABLE `resetpassword` (
  `reset_id` int(11) NOT NULL auto_increment,
  `user_id` varchar(128),
  `resetpasswordkey` varchar(250),
  `useremail` varchar(40),
  PRIMARY KEY  (`reset_id`)
)

