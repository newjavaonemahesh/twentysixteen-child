The default free themes in wordpress like twenty sixteen doesn't support the selection of an icon for every menu item But Wordpress extensively uses icons almost everywhere in the admin dashboard. As you see below, the navigation menu are plain hyperlinks. It is possible to statically bind every menu item with jquery, but a ready-made Wordpress-style solution is needed.

This is an extension that any theme can use to select a menu icon.

With this extended theme as the child theme to the Twenty Sixteen, you can see that every menu item has its own exclusive icon. This for some reason is not supported out of the box by Wordpress. There are various expensive plugins like the expensive Uber Mega Menu for rendering menus. But I never could believe that this needed to be done outside of Wordpress, when Wordpress already supports all that is needed to hook the required functionality. A look at the Wordpress dashboard that is full of icon and text combo is a proof to it.

This code is an attempt to do this.

After enabling the extended theme, go to Admin -> Menus, create a new menu. Add posts/pages, links. Expand post/page/link. You will see additional option to select an icon among 400+ font awesome icons. The extension is applied for every post/page/link to be added as a menu item.

By tweaking a bit of this code, you can add any premium font icon package like icomoon.

This is not a plugin, but will be great if someone can make it into a plugin. It is always easy to fall for a plugin, that does comprehensive things, but one loses the flexibility that way.