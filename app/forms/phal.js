if (!__Phal) {

	var __Phal = true;

	/*
	 * Class, version 2.7 Copyright (c) 2006, 2007, 2008, Alex Arnell
	 * <alex@twologic.com> Licensed under the new BSD License. See end of file
	 * for full license terms.
	 */

	var Class = (function() {
		var __extending = {};

		return {
			extend : function(parent, def) {
				if (arguments.length == 1) {
					def = parent;
					parent = null;
				}
				var func = function() {
					if (arguments[0] == __extending) {
						return;
					}
					this.initialize.apply(this, arguments);
				};
				if (typeof (parent) == 'function') {
					func.prototype = new parent(__extending);
				}
				var mixins = [];
				if (def && def.include) {
					if (def.include.reverse) {
						// methods defined in later mixins should override prior
						mixins = mixins.concat(def.include.reverse());
					} else {
						mixins.push(def.include);
					}
					delete def.include; // clean syntax sugar
				}
				if (def)
					Class.inherit(func.prototype, def);
				for ( var i = 0; (mixin = mixins[i]); i++) {
					Class.mixin(func.prototype, mixin);
				}
				return func;
			},
			mixin : function(dest, src, clobber) {
				clobber = clobber || false;
				if (typeof (src) != 'undefined' && src !== null) {
					for ( var prop in src) {
						if (clobber
								|| (!dest[prop] && typeof (src[prop]) == 'function')) {
							dest[prop] = src[prop];
						}
					}
				}
				return dest;
			},
			inherit : function(dest, src, fname) {
				if (arguments.length == 3) {
					var ancestor = dest[fname], descendent = src[fname], method = descendent;
					descendent = function() {
						var ref = this.parent;
						this.parent = ancestor;
						var result = method.apply(this, arguments);
						ref ? this.parent = ref : delete this.parent;
						return result;
					};
					// mask the underlying method
					descendent.valueOf = function() {
						return method;
					};
					descendent.toString = function() {
						return method.toString();
					};
					dest[fname] = descendent;
				} else {
					for ( var prop in src) {
						if (dest[prop] && typeof (src[prop]) == 'function') {
							Class.inherit(dest, src, prop);
						} else {
							dest[prop] = src[prop];
						}
					}
				}
				return dest;
			},
			singleton : function() {
				var args = arguments;
				if (args.length == 2 && args[0].getInstance) {
					var klass = args[0].getInstance(__extending);
					// we're extending a singleton swap it out for it's class
					if (klass) {
						args[0] = klass;
					}
				}

				return (function(args) {
					// store instance and class in private variables
					var instance = false;
					var klass = Class.extend.apply(args.callee, args);
					return {
						getInstance : function() {
							if (arguments[0] == __extending)
								return klass;
							if (instance)
								return instance;
							return (instance = new klass());
						}
					};
				})(args);
			}
		};
	})();

	// finally remap Class.create for backward compatability with prototype
	Class.create = function() {
		return Class.extend.apply(this, arguments);
	};

	/*
	 * Redistribution and use in source and binary forms, with or without
	 * modification, are permitted provided that the following conditions are
	 * met:
	 * 
	 * Redistributions of source code must retain the above copyright notice,
	 * this list of conditions and the following disclaimer. Redistributions in
	 * binary form must reproduce the above copyright notice, this list of
	 * conditions and the following disclaimer in the documentation and/or other
	 * materials provided with the distribution. Neither the name of
	 * typicalnoise.com nor the names of its contributors may be used to endorse
	 * or promote products derived from this software without specific prior
	 * written permission.
	 * 
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
	 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
	 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
	 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
	 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
	 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
	 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
	 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
	 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
	 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	 */

	var __Ajax = Class.extend({

		initialize : function() {
			self._defaultData = {
				method : 'get',
				parameters : {},
				requestHeaders : {},
				onInteractive : null,
				onSuccess : null,
				onFailure : null
			};
		},

		request : function(url, data) {

			var xhr = this._createXHR();

			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) {
					if (data.onSuccess != null) {
						data.onSuccess(xhr.responseText);
					}
				}
			}
			var str = [];
			for ( var key in data.parameters) {
				str.push(encodeURIComponent(key) + "="
						+ encodeURIComponent(data.parameters[key]));
			}
			var params = str.join("&");

			xhr.open(data.method, url, true);
			for ( var key in data.requestHeaders) {
				xhr.setRequestHeader(key, data.requestHeaders[key]);
			}
			xhr.setRequestHeader("Content-type",
					"application/x-www-form-urlencoded");
			xhr.setRequestHeader("X_REQUESTED_WITH", "XMLHttpRequest");
			xhr.send(params);
		},

		_createXHR : function() {
			var xhr;
			if (window.ActiveXObject) {
				try {
					xhr = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					xhr = null;
				}
			} else {
				xhr = new XMLHttpRequest();
			}

			return xhr;
		}

	});

	/**
	 * Lock: A Unified Locking Library Thanks to the magic of the event stack in
	 * Firefox / IE, it is possible to have your data be changed behind your
	 * back when using browser window events. A basic lock will help stop that.
	 * An object is returned to the requesting application which will say if a
	 * lock was obtained or not.
	 * 
	 * This class is licensed under the New BSD License:
	 * http://www.opensource.org/licenses/bsd-license.html
	 * 
	 * Copyright (c) 2007 Jakob Heuser (jakob@felocity.org). All rights
	 * reserved.
	 */
	var Lock = function() {
		var locks = {};

		var normalize_namespace = function(name) {
			return ("c" + name).replace(/[^a-z0-9\-\_]/gi, "");
		};

		return {
			declare : function() {
				for ( var i = 0; i < arguments.length; i++) {
					if (!locks[normalize_namespace(arguments[i])]) {
						locks[normalize_namespace(arguments[i])] = new Array();
					}
				}
			},
			obtain : function(space) {
				// atomic assignment, no 2 objects are same
				var lock = new Object();

				// no namespace? problem
				space = normalize_namespace(space);
				if (!locks[space]) {
					throw "Namespaces must be declared before getting into locks.";
				}

				// atomic op for as long as JS is single threaded
				// whenever JS multi-threads, this one call is synchronized
				locks[space].push(lock);

				// safely clean lock_owner
				if (locks[space][0] === lock) {
					locks[space] = [ locks[space][0] ];
				}

				var lock_obj = {
					isOwner : function() {
						return (locks[space][0] === lock);
					},
					release : function() {
						if (locks[space][0] === lock) {
							locks[space] = new Array();
						}
					}
				};

				return lock_obj;
			}
		};
	}();

	/**
	 * Just In Time (JIT) Loader JIT makes it easy to load one or more
	 * JavaScript files on demand. It's goal is to encourage developers to only
	 * load scripts when they need to as opposed to overloading the HEAD of
	 * their document.
	 * 
	 * Many of the base loading concepts are attrributed to LazyLoad, developed
	 * by Ryan Grove, please see copyright information below for additional
	 * details. This software is licensed under the New BSD License:
	 * http://www.opensource.org/licenses/bsd-license.html
	 * 
	 * LazyLoad segments Copyright (c) 2007 Ryan Grove (ryan@wonko.com). All
	 * rights reserved. JIT segments Copyright (c) 2007 Jakob Heuser
	 * (jakob@felocity.org). All rights reserved.
	 * 
	 * For additional details, please check out the following usage guides:
	 * LazyLoad: http://wonko.com/article/527 JITLoad:
	 * http://www.felocity.org/blog/article/just_in_time_loader_for_javascript/
	 * 
	 * Version: 1.0.0; 1.0.3 (LazyLoad)
	 */

	var JIT = function() {

		/**
		 * Denotes an object that is pending a requestComplete() call it is null
		 * if there is no request in progress
		 */
		var pending = {};

		/**
		 * A mutable array of the current script IDs in use. This makes cleanup
		 * of the scripts after they have finished loading easier.
		 */
		var script_ids = {};

		/**
		 * A counter for the total number of scripts we have created. Helps to
		 * ensure clean loading without collisions.
		 */
		var script_id_counter = 0;

		/**
		 * A variable that defines the lock owner. Using the counter and owner,
		 * a function can determine if they are the lock.
		 */
		var lock_owner = null;

		/**
		 * The prefix our our custom IDs. This ensures we don't collide w/ stuff
		 */
		var script_id_prefix = "jit-gen";

		/**
		 * An index of URLs that have been loaded At the expense of more memory,
		 * this speeds up scanning for all included scripts
		 */
		var loaded_scripts = {};

		/**
		 * sets an IE version based on
		 * 
		 * @_jscript_version replace if cc ever gets IE versioning, this is used
		 *                   for CSS
		 */
		var IEVersion = /*
						 * @cc_on function(){ switch(@_jscript_version){ case
						 * 1.0:return 3; case 3.0:return 4; case 5.0:return 5;
						 * case 5.1:return 5; case 5.5:return 5.5; case
						 * 5.6:return 6; case 5.7:return 7; }}()||@
						 */0;

		/**
		 * Container for holding the document head, so we only do it once
		 */
		var document_head = null;

		/**
		 * Declare our locks
		 */
		var LOCK_WRITING_TO_DOM = "JIT_dom_write";
		var LOCK_DOM_CLEANUP = "JIT_dom_clean";
		var LOCK_GET_SEQUENCE_ID = "JIT_sequence_id";
		Lock.declare(LOCK_GET_SEQUENCE_ID, LOCK_WRITING_TO_DOM,
				LOCK_DOM_CLEANUP);

		/**
		 * creates a unique ID using the counter and prefix, runs inside of a
		 * dom write space
		 * 
		 * @return {string}
		 */
		var generateId = function() {
			script_id_counter++;
			return script_id_prefix + script_id_counter;
		};

		/**
		 * Detect all loaded scripts, and add their URLs to the list If there is
		 * a faster method than getting the elements by tag name it should be
		 * used instead. This runs on loadOnce so that we can see if other
		 * scripts also added their own JS.
		 * 
		 * @return {null}
		 */
		var detectLoadedScripts = function() {
			var script_nodes = document.getElementsByTagName("script");
			var css_nodes = document.getElementsByTagName("link");

			for ( var i = 0; i < script_nodes.length; i++) {
				// skip sourcelss scripts
				var node = script_nodes[i];
				if (!node.src || node.src.length == 0) {
					continue;
				}
				loaded_scripts[normalizeScriptPath(node.src)] = true;
			}
			for ( var j = 0; j < css_nodes.length; j++) {
				// skip sourcelss css or wrong types
				var node = css_nodes[j];

				if (!node.href || node.href.length == 0 || !node.rel
						|| node.rel.toString().toLowerCase() != "stylesheet"
						|| !node.type
						|| node.type.toString().toLowerCase() != "text/css") {

					continue;
				}

				loaded_scripts[normalizeScriptPath(node.href)] = true;
			}
		};

		/**
		 * A helper function which normalizes the script path the resulting path
		 * can be used as a property name in an object
		 * 
		 * @param {string}
		 *            path to normalize
		 * @return {string} normalized path
		 */
		var normalizeScriptPath = function(path) {
			return "s" + escape(path);
		};

		/**
		 * Handle a load function for Javascript or CSS
		 * 
		 * @see load
		 * @see loadOnce
		 * @param {type}
		 *            a type to load, either script or css
		 * @param {boolean}
		 *            once if true, load will ensure everything loads only once
		 */
		var handleLoad = function(urls, verifier, callback, obj, scope, type,
				once) {
			// we wait on document.body, otherwise we can't be certain we
			// have
			// a closed HEAD tag in IE6 for insertion
			if (!document.body) {
				window.setTimeout(
						function() {
							handleLoad(urls, verifier, callback, obj, scope,
									type, once);
						}, 50);
				return;
			}

			// ---------------------------
			// ---- BEGIN CRTICIAL SECTION
			// ---------------------------

			// if you are not the lock owner, then your request goes into
			// wait
			// mode
			// techincally a spinlock. Wait is fixed at 10ms right now, can
			// change
			// to reflect number of current "threads" later
			var lock = Lock.obtain(LOCK_GET_SEQUENCE_ID);
			if (!lock.isOwner()) {
				window.setTimeout(
						function() {
							handleLoad(urls, verifier, callback, obj, scope,
									type, once);
						}, 10);
				return;
			}

			// obtained lock

			// assign the document head if we haven't yet
			if (!document_head) {
				document_head = document.getElementsByTagName('head')[0];
			}

			// get an ID for our sequence
			var sequence_id = generateId();
			pending[sequence_id] = {};
			script_ids[sequence_id] = [];

			// scrape all loaded scripts in case things have changed
			detectLoadedScripts();

			// done, we completed critical code section
			lock.release();

			// -------------------------
			// ---- END CRTICIAL SECTION
			// -------------------------

			// cast URLs to an array if we need to
			urls = (urls.constructor === Array) ? urls : [ urls ];

			// if verifier was skipped or nulled, then we need to make one
			if (!verifier || typeof (verifier) != "function") {
				verifier = function() {
					return true;
				};
			}

			// hold onto the pending object for requestComplete and
			// loadComplete
			pending[sequence_id] = {
				urls : urls,
				verifier : verifier,
				callback : callback,
				obj : obj,
				scope : scope,
				type : type,
				once : once,
				lock : lock
			};

			// if we are running in loadOnce mode
			if (once) {
				var urls_to_load = [];
				for ( var i = 0; i < urls.length; i += 1) {
					var loaded = (loaded_scripts[normalizeScriptPath(urls[i])]) ? true
							: false;
					if (!loaded) {
						urls_to_load.push(urls[i]);
					}
				}

				// do we have any URLs to load? If not, loading is complete
				// and
				// we are done
				if (urls_to_load.length <= 0) {
					loadComplete(sequence_id);
					return;
				}

				// there is stuff to load still
				// redefine URls by our new definition, and our pending
				urls = urls_to_load;
				pending[sequence_id] = {
					urls : urls,
					verifier : verifier,
					callback : callback,
					obj : obj,
					scope : scope,
					type : type,
					once : once,
					lock : lock
				};
			}

			if (type == "js") {
				insertScripts(urls, sequence_id);
			} else if (type == "css"
					|| (type.match(/^css/i) && type == "css" + IEVersion)) {
				insertStyles(urls, sequence_id);
			} else {
				// whatever we had, we can't use... release the lock
				lock.release();
				JIT.scriptsComplete(sequence_id);
			}

		};

		var insertStyles = function(urls, sequence_id) {

			// Cast urls to an Array.
			urls = urls.constructor === Array ? urls : [ urls ];

			var node;

			for ( var i = 0; i < urls.length; i += 1) {

				// create a unique ID and add to our ID list
				var sc_id = generateId();
				// script_ids.push(sc_id);

				// create the script object, and append it to the head
				node = document.createElement('link');
				node.id = sc_id;
				node.href = urls[i];
				node.rel = "stylesheet";
				node.type = "text/css";
				node.media = "screen";

				writeNode(node);
			}

			// in MSIE, we will need to listen to the onreadystatechange
			// if the file is cached, we may not even see "loaded" as an
			// option
			// and may instead see "complete". Because of this, we need to
			// scan
			// for both. Script loading is linear, so we only need to watch
			// the last script we were inserting
			if (IEVersion) {
				node.onreadystatechange = function() {
					if (this.readyState == 'loaded'
							|| this.readyState == 'complete') {
						JIT.scriptsComplete(sequence_id);
					}
				};
			} else {
				// this is a non MSIE browser. We can use a safer method of
				// detecting when a script is done. We insert a small
				// scriptlet
				// at the end of all our script objects which executes the
				// requestComplete() code.
				var sc_id = generateId();
				script_ids[sequence_id].push(sc_id);

				var smart_script = document.createElement('script');
				smart_script.id = sc_id;
				smart_script.type = "text/javascript";
				smart_script.appendChild(document
						.createTextNode("JIT.scriptsComplete('" + sequence_id
								+ "');"));

				writeNode(smart_script);
			}

			// release DOM writing lock
			if (pending[sequence_id]) {
				pending[sequence_id].lock.release();
			}
		};

		var insertScripts = function(urls, sequence_id) {

			// Cast urls to an Array.
			urls = urls.constructor === Array ? urls : [ urls ];

			// Load the scripts at the specified URLs.
			var script;

			for ( var i = 0; i < urls.length; i += 1) {

				// create a unique ID and add to our ID list
				var sc_id = generateId();
				script_ids[sequence_id].push(sc_id);

				// create the script object, and append it to the head
				script = document.createElement('script');
				script.id = sc_id;
				script.src = urls[i];
				script.type = "text/javascript";

				writeNode(script);
			}

			// no script at this point, we're in trouble
			if (!script) {
				// release DOM writing lock
				if (pending[sequence_id]) {
					pending[sequence_id].lock.release();
				}
				return;
			}

			// in MSIE, we will need to listen to the onreadystatechange
			// if the file is cached, we may not even see "loaded" as an
			// option
			// and may instead see "complete". Because of this, we need to
			// scan
			// for both. Script loading is linear, so we only need to watch
			// the last script we were inserting
			if (IEVersion) {
				script.onreadystatechange = function() {
					if (this.readyState == 'loaded'
							|| this.readyState == 'complete') {
						JIT.scriptsComplete(sequence_id);
					}
				};
			} else {
				// this is a non MSIE browser. We can use a safer method of
				// detecting when a script is done. We insert a small
				// scriptlet
				// at the end of all our script objects which executes the
				// requestComplete() code.
				var sc_id = generateId();
				script_ids[sequence_id].push(sc_id);

				var smart_script = document.createElement('script');
				smart_script.id = sc_id;
				smart_script.type = "text/javascript";
				smart_script.appendChild(document
						.createTextNode("JIT.scriptsComplete('" + sequence_id
								+ "');"));

				writeNode(smart_script);
			}

			// release DOM writing lock
			if (pending[sequence_id]) {
				pending[sequence_id].lock.release();
			}
		};

		var writeNode = function(node) {
			var timer = null;
			var retry_in = 100;
			// a function that tries to get a lock for DOM write
			// once it does, it inserts
			var processWrite = function() {
				// ---------------------------
				// ---- BEGIN CRTICIAL SECTION
				// ---------------------------
				var lock = Lock.obtain(LOCK_WRITING_TO_DOM);
				if (!lock.isOwner()) {
					timer = window.setTimeout(processWrite, retry_in);
					return;
				}
				window.clearTimeout(timer);
				timer = null;
				document_head.appendChild(node);
				lock.release();
			};

			// calls processWrite on a setTimeout that lets other events run
			timer = window.setTimeout(processWrite, retry_in);
		};

		/**
		 * A helper function which completes the request it fires off any
		 * callbacks that are required, and hands off the lock to the next
		 * request in line
		 */
		var loadComplete = function(sequence_id, lock) {
			// there is a theoretical window where we could resolve a
			// loadComplete
			// with a loadComplete waiting... if that happens, just return
			if (!pending[sequence_id]) {
				return;
			}

			// try and lock on DOM cleanup
			if (!lock) {
				var lock = Lock.obtain(LOCK_DOM_CLEANUP);
			}

			// if not owner, try again with getting a new lock
			if (!lock.isOwner()) {
				window.setTimeout(function() {
					loadComplete(sequence_id);
				}, 10);
				return;
			}

			// run the current verifier until it passes
			if (pending[sequence_id].verifier
					&& !pending[sequence_id].verifier.call(window)) {
				// did not pass, try again in X seconds using same (valid)
				// lock
				window.setTimeout(function() {
					loadComplete(sequence_id, lock);
				}, 100);
				return;
			}

			// release DOM writing lock if not done
			if (pending[sequence_id]) {
				pending[sequence_id].lock.release();
			}

			// redetect our loaded scripts at this point
			detectLoadedScripts();

			// remove any script IDs we have made, they are all done... I
			// mean,
			// if
			// there are any
			if (script_ids[sequence_id]) {
				while (script_ids[sequence_id].length > 0) {
					var sc_id = script_ids[sequence_id].shift();
					var script = document.getElementById(sc_id);
					if (typeof script != 'undefined') {
						script.parentNode.removeChild(script);
					}
				}
				script_ids[sequence_id] = null;
			}

			// Execute the callback.
			if (pending[sequence_id] && pending[sequence_id].callback) {
				if (pending[sequence_id].obj) {
					if (pending[sequence_id].scope) {
						pending[sequence_id].callback
								.call(pending[sequence_id].obj);
					} else {
						pending[sequence_id].callback.call(window,
								pending[sequence_id].obj);
					}
				} else {
					pending[sequence_id].callback.call();
				}
			}

			// clear our pending object for the next request
			// not required, just nice to clean
			pending[sequence_id] = null;

			// release the lock
			lock.release();
		};

		/**
		 * Returns a batch object for chain processing. the returned object is
		 * the easiest to work with in the JIT loader and its functionality is
		 * documented similarly to JIT. However, for load, loadOnce, and addCSS,
		 * there is no callbacks involved. Instead, the system uses its run
		 * function as a callback method to unfurl the stack created
		 * 
		 * @return JIT Batch Object
		 * @see JIT.load
		 * @see JIT.loadOnce
		 * @see JIT.addCSS
		 */
		var JIT_Chain = function() {
			var stack = [];
			var run_callback = null;
			var run_object = null;
			var run_scope = null;

			// return object
			return {
				load : function(urls, verifier) {
					stack.push({
						type : "js",
						once : false,
						urls : urls,
						verifier : verifier
					});
					return this;
				},
				loadOnce : function(urls, verifier) {
					stack.push({
						type : "js",
						once : true,
						urls : urls,
						verifier : verifier
					});
					return this;
				},
				addCSS : function(urls, verifier, ie_version) {
					stack.push({
						type : "css",
						once : true,
						urls : urls,
						verifier : verifier,
						ie : ie_version
					});
					return this;
				},

				/**
				 * Executes the stack of objects, using a basic form of
				 * recursion
				 * 
				 * @param function
				 *            the callback function to run
				 * @param obj
				 *            the object to include in the callback
				 * @param scope
				 *            if true, the callback will be ran in the object's
				 *            scope
				 */
				onComplete : function(callback, obj, scope) {
					var that = this;

					// store the run callback the first time we enter the
					// onComplete
					if (!run_callback) {

						if (!callback) {
							callback = function() {
							};
						}
						run_callback = callback;
						run_object = obj;
						run_scpe = scope;
					}

					// no stack, we are done, run the callback
					if (stack.length == 0) {
						if (obj) {
							if (scope) {
								run_callback.call(obj);
							} else {
								run_callback.call(window, obj);
							}
						} else {
							run_callback.call();
						}

						return;
					}

					// start unstacking
					var next_call = stack.shift();

					// call a run op for this
					if (next_call.type == "js") {
						if (next_call.once) {
							JIT.loadOnce(next_call.urls, next_call.verifier,
									that.onComplete, that, true);
						} else {
							JIT.load(next_call.urls, next_call.verifier,
									that.onComplete, that, true);
						}
					} else if (next_call.type == "css") {
						if (next_call.ie) {
							JIT.addCSS(next_call.urls, next_call.verifier,
									that.onComplete, that, true, next_call.ie);
						} else {
							JIT.addCSS(next_call.urls, next_call.verifier,
									that.onComplete, that, true);
						}
					}
				}
			};
		};

		// begin public interface
		return {
			/**
			 * Loads the specified script(s) and then sets up a call to
			 * requestComplete this is the meat of the JIT loader.
			 * 
			 * @param {string|array}
			 *            the URLs to load
			 * @param {function}
			 *            verifier a funtion definition that asserts load is
			 *            done
			 * @param {function}
			 *            callback a function definition to call when loaded
			 * @param {object}
			 *            obj an object to pass to the callback function
			 *            [object]
			 * @param {boolean}
			 *            scope if true, *callback* will be scoped to *obj*
			 */
			load : function(urls, verifier, callback, obj, scope) {
				handleLoad(urls, verifier, callback, obj, scope, "js", false);
			},

			addCSS : function(urls, verifier, callback, obj, scope, ie_restrict) {
				if (!ie_restrict) {
					ie_restrict = "";
				}
				handleLoad(urls, verifier, callback, obj, scope, "css"
						+ ie_restrict, true);
			},

			/**
			 * Load a script only once. If that script has existed in our
			 * document. When initialized, we poll and take a capture of all
			 * scripts. If any urls are found, they will be discarded.
			 * 
			 * @param {string|array}
			 *            the URLs to load
			 * @param {function}
			 *            verifier a funtion definition that asserts load is
			 *            done
			 * @param {function}
			 *            callback a function definition to call when loaded
			 * @param {object}
			 *            obj an object to pass to the callback function
			 *            [object]
			 * @param {boolean}
			 *            scope if true, *callback* will be scoped to *obj*
			 */
			loadOnce : function(urls, verifier, callback, obj, scope) {
				handleLoad(urls, verifier, callback, obj, scope, "js", true);
			},

			startChain : function() {
				return JIT_Chain();
			},

			/**
			 * Runs the current verifier until it passes, then calls
			 * loadComplete
			 */
			scriptsComplete : function(sequence_id) {
				// loadComplete call to hand off and clean up
				loadComplete(sequence_id);
			}
		};
	}();

	
	/**
	 * This class handles the synchronization between the client and the server,
	 * by sending all client values + events to the server and update the client
	 * with the server responses.
	 * 
	 */
	var __ClientEventHandler = Class
			.extend({

				initialize : function() {
					this.code = null;
					this.viewCode = null;
					this.url = "index.ajax";
					this.debug = false;
					this.flowExecutionKey = null;
				},

				setCode : function(code) {
					this.code = code;
				},

				getCode : function() {
					return this.code;
				},

				setViewCode : function(viewCode) {
					this.viewCode = viewCode;
				},

				getViewCode : function() {
					return this.viewCode;
				},

				setFlowExecutionKey : function(flowExecutionKey) {
					this.flowExecutionKey = flowExecutionKey;
				},

				getFlowExecutionKey : function() {
					return this.flowExecutionKey;
				},

				setUrl : function(url) {
					this.url = url;
				},

				getUrl : function() {
					return this.url;
				},

				setDebug : function(debug) {
					this.debug = debug;
				},

				getDebug : function() {
					return this.debug;
				},
				
				extractScripts : function(htmlContent) {
					var returnValue = new Array();
					var scriptRegExp = /<script[^>]*>([\S\s]*?)<\/script>/g;
					match = scriptRegExp.exec(htmlContent);
					var i = 0;
					while (match != null) {
						returnValue[i] = match[1];
						i = i + 1;
						match = scriptRegExp.exec(htmlContent);
					}
					return returnValue;
				},

				updateHtmlContent : function(elementId, value) {
					var valueHolder = document.getElementById(elementId);
					if (typeof (valueHolder) != 'undefined') {
						valueHolder.innerHTML = value;
						var scripts = this.extractScripts(value);
						for ( var key in scripts) {
							var script = scripts[key];
							try {
								eval(script);
							} catch (e) {
							    if (e instanceof SyntaxError && this.debug) {
							        alert('Syntax error on javascript returned via ajax: ' + e.message);
							    }
							}				
						}
					}
				},

				call : function(viewCode, service, arguments, callback) {
					var ajax = new __Ajax();
					if(!callback) {
						callback = function() { return true; }
					}
					ajax.request(this.url,
					{
						method : 'post',
						parameters : {
							viewCode : viewCode,
							_flowExecutionKey : this.getFlowExecutionKey(),
							service: service,
							arguments: JSON.stringify(arguments),
						},
						requestHeaders : {
							Accept : 'text/javascript, text/html, application/xml, text/xml, application/json, */*'
						},
						onSuccess : this.handleServerResponse.bind(this, callback)
					});
				},
				
				handleServerResponse : function(callback, serverResponse) {
					if(!!callback) {
						if(!!serverResponse) {
							serverResponse = JSON.parse(serverResponse)
						}
						callback(serverResponse);
					}
				}
				
			});

	/**
	 * Static method to retrieve a __ClientEventHandler singleton instance
	 * 
	 */
	__ClientEventHandler.getInstance = function() {
		if (!__ClientEventHandler.hasOwnProperty('instance')) {
			__ClientEventHandler.instance = new __ClientEventHandler();
		}
		return __ClientEventHandler.instance;
	};

	// **************************************************
	// Utility classes: ////////////////////////////////
	// **************************************************

	var DomLoaded = {
		onload : [],
		loadComplete : false,
		listeningEvent : false,

		isLoaded : function() {
			return DomLoaded.loadComplete;
		},

		loaded : function() {
			DomLoaded.loadComplete = true;
			if (arguments.callee.done)
				return;
			arguments.callee.done = true;
			for ( var i = 0; i < DomLoaded.onload.length; i++) {
				DomLoaded.onload[i]();
			}
			DomLoaded.onload = [];
		},
		load : function(fireThis) {
			// if the DomLoaded event is already raised, execute the function
			// directly:
			if (DomLoaded.isLoaded()) {
				return fireThis();
			}
			// otherwise, append it to the onload stack
			DomLoaded.onload.push(fireThis);
			if (DomLoaded.listeningEvent == true)
				return;
			if (document.addEventListener)
				document.addEventListener("DOMContentLoaded", DomLoaded.loaded,
						null);
			if (/KHTML|WebKit/i.test(navigator.userAgent)) {
				var _timer = setInterval(function() {
					if (/loaded|complete/.test(document.readyState)) {
						clearInterval(_timer);
						delete _timer;
						DomLoaded.loaded();
					}
				}, 10);
			}
			/* @cc_on @ */
			/*
			 * @if (@_win32) var proto = "src='javascript:void(0)'"; if
			 * (location.protocol == "https:") proto = "src=//0";
			 * document.write("<scr"+"ipt id=__ie_onload defer " + proto + "><\/scr"+"ipt>");
			 * var script = document.getElementById("__ie_onload");
			 * script.onreadystatechange = function() { if (this.readyState ==
			 * "complete") { DomLoaded.loaded(); } }; /*@end @
			 */
			window.onload = DomLoaded.loaded;
			DomLoaded.listeningEvent = true;
		}
	};

	
}
