(() => {

    let $NAMESPACE$ = (() => {

        let routes = {

            absolute: $ABSOLUTE$,
            rootUrl: '$ROOTURL$',
            routes: $ROUTES$,
            prefix: '$PREFIX$',

            route: function (name, parameters, route) {
                route = route || this.getByName(name);

                return route ? this.toRoute(route, parameters) : undefined;
            },

            url: function (url, parameters) {
                let paramIsEmptyObj = Object.keys(parameters).length === 0 && parameters.constructor === Object;
                let uri = !parameters || paramIsEmptyObj ? url : url.concat('/' + parameters.join('/'));

                return this.getCorrectUrl(uri);
            },

            toRoute: function (route, parameters) {
                let uri = this.replaceNamedParameters(route.uri, parameters);
                let qs = this.getRouteQueryString(parameters);

                return this.absolute && this.isOtherHost(route)
                    ? "//" + route.host + "/" + uri + qs
                    : this.getCorrectUrl(uri + qs);
            },

            isOtherHost: function (route) {
                return route.host && route.host !== window.location.hostname;
            },

            replaceNamedParameters: function (uri, parameters) {
                uri = uri.replace(/{(.*?)\??}/g, function (match, key) {
                    if (parameters.hasOwnProperty(key)) {
                        let value = parameters[key];
                        delete parameters[key];
                        return value;
                    } else {
                        return match;
                    }
                });

                // Strip out any optional parameters that were not given
                return uri.replace(/\/{.*?\?}/g, '');
            },

            getRouteQueryString: function (parameters) {
                let qs = [];
                for (let key in parameters) {
                    if (parameters.hasOwnProperty(key)) {
                        qs.push(key + '=' + parameters[key]);
                    }
                }

                return qs.length < 1 ? '' : '?' + qs.join('&');
            },

            getByName: function (name) {
                for (let key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].name === name) {
                        return this.routes[key];
                    }
                }
            },

            getByAction: function (action) {
                for (let key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].action === action) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function (uri) {
                let url = this.prefix + '/' + uri.replace(/^\/?/, '');

                return this.absolute
                    ? this.rootUrl.replace('/\/?$/', '') + url
                    : url;
            }
        };

        let getLinkAttributes = function (attributes) {
            if (!attributes) {
                return '';
            }

            let attrs = [];
            for (let key in attributes) {
                if (attributes.hasOwnProperty(key)) {
                    attrs.push(key + '="' + attributes[key] + '"');
                }
            }

            return attrs.join(' ');
        };

        let getHtmlLink = function (url, title, attributes) {
            title = title || url;

            return '<a href="' + url + '" ' + getLinkAttributes(attributes) + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // $NAMESPACE$.action('HomeController@getIndex', [params = {}])
            action: function (name, parameters) {
                return routes.route(name, parameters || {}, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // $NAMESPACE$.route('routeName', [params = {}])
            route: function (route, parameters) {
                return routes.route(route, parameters || {});
            },

            // Generate a fully qualified URL to the given path.
            // $NAMESPACE$.route('url', [params = {}])
            url: function (route, parameters) {
                return routes.url(route, parameters || {});
            },

            // Generate a html link to the given url.
            // $NAMESPACE$.link_to('foo/bar', [title = url], [attributes = {}])
            link_to: function (url, title, attributes) {
                return getHtmlLink(this.url(url), title, attributes);
            },

            // Generate a html link to the given route.
            // $NAMESPACE$.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route: function (route, title, parameters, attributes) {
                return getHtmlLink(this.route(route, parameters), title, attributes);
            },

            // Generate a html link to the given controller action.
            // $NAMESPACE$.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action: function (action, title, parameters, attributes) {
                return getHtmlLink(this.action(action, parameters), title, attributes);
            }
        };
    }).call(this);

    /**
     * Expose the class either via AMD, CommonJS or the global object
     *
     * tests - global test array in karma.js
     */
    if (typeof define === 'function' && define.amd && !tests) {
        define(function () {
            return $NAMESPACE$;
        });
    } else if (typeof module === 'object' && module.exports && !tests) {
        module.exports = $NAMESPACE$;
    } else {
        window.$NAMESPACE$ = $NAMESPACE$;
    }

}).call(this);

