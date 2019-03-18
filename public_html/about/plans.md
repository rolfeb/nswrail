# Plans for the site...

So... I've completed the first phase of the site overhaul. The site has been 
pretty much rewritten to be much cleaner and more maintainable.
In particular, it should work well on a mobile device.
I've got a longish task list of things to address, but it is at a point where
it makes sense to push out the new version.

Going forward, I won't have as much time to devote to maintenance as I did
when the site was active. To manage this, I plan to do the following to 
reduce my workload:

1.  Allow people to upload their own photos. Anyone who wants to do this will
    need to register an account.  Falling out from this, a bunch of things need
    to be done:
    - Allow users to securely create accounts, reset passwords etc.
    - Allow registered users to upload and remove their photos.
    - Put in place some mechanism to allow users to flag when inappropriate
        images have been uploaded by someone.
    - Migrate existing users/photos to this mechanism, so that they can
        maintain their existing photos.
2.  Set up a email service so that people who want to use photos can contact
    the copyright owner (without their email address being leaked). Then
    I can get out of the loop for that process.
3.  Allow approved users to edit technical details, so that this kind of
    information doesn't get too out of date.
4.  Push the site code up to github so that people who are really keen
    can help maintain the site (that's more in hope than expectation...). 
