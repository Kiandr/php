/**
 * Plugin.js
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/*jshint smarttabs:true, undef:true, unused:true, latedef:true, curly:true, bitwise:true, camelcase:false, loopfunc:true */
/*global moxman:true, Dropbox:true */

moxman.require([
	"moxman/PluginManager",
	"moxman/util/Path",
	"moxman/util/Loader",
	"moxman/util/JsonRpc"
], function(PluginManager, Path, Loader, JsonRpc) {
	PluginManager.add("dropbox", function(manager) {
		JsonRpc.exec("dropbox.getOptions", {}, function(options) {
			function uploadFile() {
				Loader.loadScript({
					src: "//www.dropbox.com/static/api/1/dropbox.js",
					id: "dropboxjs",
					"data-app-key": options.app_id
				}, function() {
					Dropbox.choose({
						linkType: "direct",
						extensions: options.extensions,
						success: function(files) {
							var toPath = Path.join(manager.currentDir.path, files[0].name);
							manager.showThrobber();
							JsonRpc.exec("importFromUrl", {
								url: files[0].link,
								path: toPath
							}, function() {
								manager.refresh(function() {
									manager.selectByPath(toPath);
									manager.hideThrobber();
								});
							});
						}
					});
				});
			}
			if (options.app_id.length > 0) {
				manager.addMenuItem({
					text: 'Dropbox',
					icon: 'dropbox',
					onclick: uploadFile,
					contexts: ['upload']
				});
			}
		});
	});
});