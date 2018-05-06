# Sample Makefile for Web Programming students
#
# by Darren Provine, 14 September 2002

# These lines should be the only ones you have to change for many
# projects.

DESTDIR = /home/sridharah9/public_html/awp/Project
TARGETS = confirm_user.php delete_account.php delete_comment.php delete_photo.php favicon.ico footer.php forgot_password.php forgot_password_success.php get_comment.php header.php helpers.php index.php login.php logout.php photo.php connect.php php.ini post_comment.php register.php registration_success.php reset_password.php scripts.php search.php search_users.php stylesheets.php template.php upload_file.php jquery-3.2.1.min.js jquery.textcomplete.min.js jquery.mentionsinput.js foundation.min.js moment.min.js index.js foundation.min.css foundation-icons.css common.css register.css login.css index.css foundation-icons.eot foundation-icons.svg foundation-icons.ttf foundation-icons.woff default.png
SOURCES = confirm_user.php delete_account.php delete_comment.php delete_photo.php favicon.ico footer.php forgot_password.php forgot_password_success.php get_comment.php header.php helpers.php index.php login.php logout.php photo.php connect.php php.ini post_comment.php register.php registration_success.php reset_password.php scripts.php search.php search_users.php stylesheets.php template.php upload_file.php jquery-3.2.1.min.js jquery.textcomplete.min.js jquery.mentionsinput.js foundation.min.js moment.min.js index.js foundation.min.css foundation-icons.css common.css register.css login.css index.css default.png
UP_DIR  = $(DESTDIR)/images
SAVEDIR = $(UP_DIR)/archive


# This target is just here to be the top target in the Makefile.
# There's nothing to compile for this one.
all: $(TARGETS)

# You might want to look up mkdir(1) to see about that -p flag.
install: $(TARGETS)
	@if [ ! -d $(DESTDIR) ] ; then mkdir -p $(DESTDIR); fi
	@if [ ! -d $(UP_DIR) ] ; then mkdir -p $(UP_DIR); \
            chmod 701 $(UP_DIR); fi
	@if [ ! -d $(SAVEDIR) ] ; then mkdir -p $(SAVEDIR); \
            chmod 1777 $(SAVEDIR); fi
	@for f in $(TARGETS)                 ; \
	do                                     \
		/usr/bin/install -v -t $(DESTDIR) -m 755 $$f ; \
	done

# Note that here we don't blow away the directory, and so we
# be sure and tell the user.  The reason not to delete the
# directory is that it may have other files in it.  Checking
# for, and deleting, any such files will have to be done manually.
# (How could this be improved?)
#
# Note also that the @ sign keeps the echo lines from being echoed
# before they are run.  (That could be confusing.)  This little
# trick (and many more) can be discovered by consulting make(1S).
deinstall:
	cd $(DESTDIR) ; /bin/rm -f $(TARGETS)
@echo "   ==>   removed file(s): $(TARGETS)"
@echo "   ==>   left directory : $(DESTDIR)"

redo: deinstall install

clean:
	/bin/rm -f core $(TARGETS)
