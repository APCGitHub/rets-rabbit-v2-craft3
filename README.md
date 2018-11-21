# Rets Rabbit Craft CMS Plugin

This plugin allows you to connect to the Rets Rabbit API(v2) in order to display your listings in a clean and intuitive way.

## Installation
1. Clone or Download the plugin.
2. Copy `craft/plugins/retsrabbit` to your plugins folder.
3. Install plugin in the Craft Control Panel under Settings > Plugins
4. Go to the Rets Rabbit settings page and add your Client ID & Secret.

### Requirements
The Rets Rabbit plugin requires at least php 5.6.

## Documentation
You can interact with the Rets Rabbit API through the `PropertiesVariable` which has the following methods.

1. [craft.retsRabbit.properties.find](#findint-id-object-resoparams-bool-usecache--false-int-cacheduration) - Single listing lookup
2. [craft.retsRabbit.properties.query](#queryobject-resoparams-bool-usecache--false-int-cacheduration) - Run a raw RESO query
3. [craft.retsRabbit.properties.search](#searchint-id-object-overrides-bool-usecache--false-int-cacheduration) - Perform a search using a saved query from a search form.

### find(*int* $id, *object* $resoParams, *bool* $useCache = false, *int* $cacheDuration)

**$id** - The MLS id of the property you want to fetch from the API.

**$resoParams** - You may pass valid RESO parameters to help filter the API results for a single listing. This can help speed up the response time if you specifically select the fields you will need from the API by using the `$select` parameter.

**$useCache** - Specify if you want the results cached.

**$cacheDuration** - Specify how long you would like the results cached for in seconds. The default is one hour.

```html
{% set listing = craft.retsRabbit.properties.find('123abc', {'$select': 'ListingId, ListPrice'}, true) %}

{% if listing is not null %}
    {{listing.ListingId}}
{% else %}
    {# An error occurred, let the user know #}
{% endif %}

```

### query(*object* $resoParams, *bool* $useCache = false, *int* $cacheDuration)

**$resoParams** - You may pass valid RESO parameters to help filter the API results for a single listing. This can help speed up the response time if you specifically select the fields you will need from the API by using the `$select` parameter.

**$useCache** - Specify if you want the results cached.

**$cacheDuration** - Specify how long you would like the results cached for in seconds. The default is one hour.

```html
{% set listings = craft.retsRabbit.properties.query({
    '$select': 'ListingId, ListPrice, PublicRemarks, StateOrProvince, City',
    '$filter': 'ListPrice ge 150000 and ListPrice le 175000 and BedroomsTotal ge 3',
    '$orderby': 'ListPrice',
    '$top': 12
}) %}

{% if listings is null %}
    {# An error occurred #}
{% else %}
    {% if listings | length %}
        {% for listing in listings %}
            <div class="card">
                <div class="card-header">
                    {{listing.ListingId}}
                </div>
                <div class="card-content">
                    {{listing.ListPrice}}
                </div>
            </div>
        {% endfor %}
    {% else %}
        {# No results for the search #}
    {% endif %}
{% endif %}
```

### search(*int* $id, *object* $overrides, *bool* $useCache = false, *int* $cacheDuration)

**$id** - The id of the saved search parameters usually pulled from a url segment.

**$overrides** - You may pass in the following RESO parameters to help tailor your query search: `$select, $orderby, $top`.

**$useCache** - Specify if you want the results cached.

**$cacheDuration** - Specify how long you would like the results cached for in seconds. The default is one hour.

```html
{# Results URL (for example): /search/results/4 #}
{% set searchId = craft.request.getSegment(3) %}
    
{% if not craft.retsRabbit.searches.exists(searchId) %}
    {% redirect '404' %}
{% endif %}

{% set perPage = 12 %}

{% set results = craft.retsRabbit.properties.search(searchId, {
    '$top': perPage,
    '$orderby': 'ListPrice desc'
}, true) %}

{% if results is null %}
    {# An error occurred #}
{% else %}
    {% if results | length %}
        {% for listing in results %}
            {# Show listing data #}
        {% endfor %}
    {% else %}
        {# No results for the search #}
    {% endif %}
{% endif %}
```

> **Note:** If you want to paginate your search results you will need to use our special [`rrPaginate` tag](#search-pagination).

### Search Form

At some point your site will need to have a search form where users enter in search criteria. We've created a markup DSL for your search HTML which will allow you to create beautiful forms for your users.

#### Required Fields

Your search form must have the following two inputs.

1. `<input type="hidden" name="action" value="retsRabbit/properties/search">
`
2. `<input type="hidden" name="redirect" value="search/results/{searchId}">`

> **Note:** Your `redirect` input must have the {searchId} term in it so that the controller endpoint which handles the form POST can redirect you to the results page with the saved search's id in the url.

We believe that the following three search types should cover the vast majority of search form use cases.

1. Single field for a single value
2. Single field for multiple values
3. Multiple fields for a single value

#### Search Form DSL

Next, let's dive into creating a search form. In general our markup DSL follows a simple pattern:

`<input name="{fieldName}(operator)" value="">`.

#### Single Field - Single Value

```html
<input name="StateOrProvince(eq)" value="">
```

This will create a query clause that looks like the following:

```json
$filter = StateOrProvince eq {value}
```

#### Single Field - Multiple Values

```html
{% set exteriorAmenities = ['Backyard', 'Pond', 'Garden'] %}

<label class="label">Exterior Features</label>
{% for feature in exteriorAmenities %}
    <div class="control">
        <label class="checkbox">
            <input type="checkbox" name="rr:ExteriorFeatures(contains)[]" value="{{feature}}">
            {{feature}}
        </label>
    </div>
{% endfor %}
```

This will create a query clause that looks like the following:

```json
$filter = (contains(ExteriorFeatures, {value1}) or contains(ExteriorFeatures, {value2})))
```

#### Multiple Fields - Single Value

```html
<input name="rr:StateOrProvince|City|PostalCode(contains)" class="input" placeholder="City, State, Zip..." type="text">
```

This will create a query clause which looks like the following:

```json
$filter = (contains(StateOrProvince, {value}) or contains(City, {value}) or contains(PostalCode, {value}))
```

> **Note:** By default, each input is treated as an independent {and} clause which are strung together to create a valid RESO query.

#### Example Search Form

The following example contains markup which will generate a form having the following capabilities:

* Run a contains search against the fields: StateOrProvince, City, PostalCode
* Run a range search (ge and/or le) against ListPrice
* Run a range search (ge) against the fields: BathroomsFull and BedroomsTotal
* Run a multi value contains search against: ExteriorFeatures
* Run a multi value contains search against: InteriorFeatures

```html
<form method="POST" action="">
    {{getCsrfInput()}}
    <input type="hidden" name="action" value="retsRabbit/properties/search">
    <input type="hidden" name="redirect" value="search/results/{searchId}">
    <div class="field">
        <div class="control has-icons-left">
            <input class="input" placeholder="City, State, Zip..." type="text" name="rr:StateOrProvince|City|PostalCode(contains)">
            <span class="icon is-left">
                <i class="fa fa-search"></i>
            </span>
        </div>
    </div>
    <div class="field is-horizontal">
        <div class="field-body">
            <div class="field">
                <div class="control is-expanded">
                    <div class="select is-fullwidth">
                        <select name="rr:ListPrice(ge)">
                            <option value="">Min Price</option>
                            {% for price in range(30000, 300000, 10000) %}
                                <option value="{{price}}">{{price | currency('USD', true)}}</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="control is-expanded">
                    <div class="select is-fullwidth">
                        <select name="rr:ListPrice(le)">
                            <option value="">Max Price</option>
                            {% for price in range(30000, 300000, 10000) %}
                                <option value="{{price}}">{{price | currency('USD', true)}}</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="field is-horizontal">
        <div class="field-body">
            <div class="field">
                <div class="control has-icons-left is-expanded">
                    <div class="select is-fullwidth">
                        <select name="rr:BathroomsFull(ge)">
                            <option value>Bathrooms</option>
                            {% for val in 0..7 %}
                                <option value="{{val}}">{{val}}+</option>
                            {% endfor %}
                        </select>
                    </div>
                    <span class="icon is-left">
                        <i class="fa fa-bath"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <div class="control has-icons-left is-expanded">
                    <div class="select is-fullwidth">
                        <select name="rr:BedroomsTotal(ge)">
                            <option value>Bedrooms</option>
                            {% for val in 0..7 %}
                                <option value="{{val}}">{{val}}+</option>
                            {% endfor %}
                        </select>
                    </div>
                    <span class="icon is-left">
                        <i class="fa fa-bed"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="field is-horizontal">
        <div class="field-body">
            <div class="field">
                <label class="label">Exterior Features</label>
                {% for feature in exteriorAmenities %}
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="rr:ExteriorFeatures(contains)[]" value="{{feature}}">
                            {{feature}}
                        </label>
                    </div>
                {% endfor %}
            </div>
            <div class="field">
                <label class="label">Interior Features</label>
                {% for feature in interiorAmenities %}
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="rr:InteriorFeatures(contains)[]" value="{{feature}}">
                            {{feature}}
                        </label>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
    <button class="button is-success" type="submit">
        <span class="icon">
            <i class="fa fa-search"></i>
        </span>
        <span>{{'Submit'|t}}</span>
    </button>
</form>
```

We used [Bulma.io](https://bulma.io/) in this example, but the above markup will generate something like the following.

![Search Form](screenshots/search-form.png "Search Form")

### Search Pagination

Because the Rets Rabbit plugin fetches data from an outside data source, it's not possible to use the native Craft pagination tag. We still believe it is very important to have the ability to paginate your results, so we created a special `rrPaginate` tag which works and looks like the native `paginate` tag in many ways.

```html
{% rrPaginate searchCriteria as pageInfo, results %}
```

#### Parameters

* [searchCriteria](#searchcriteria) - An instance of `RetsRabbit_SearchCriteriaModel`
* pageInfo - `Craft\PaginationVariable` just like with the native `pagination` tag
* results - Array of results, will be null if an error occurred and an empty array if no results were found.

#### SearchCriteria

The main difference in our `rrPaginate` tag compared to the native `paginate` tag is that it expects a `RetsRabbit_SearchCriteriaModel` as the first parameter. You can get an instance of a search criteria model in the following manner.

```html
{% set criteriaModel = craft.retsRabbit.searches.criteria() %}
```

Once you have an instance of the criteria model, you can build your query in a fluent way by chaining method calls on that `criteriaModel` object above.

```html
{#
# You must call the forId() and limit() methods for the pagination to work correctly.
#}
{% set criteria = criteriaModel
    .forId(searchId)
    .select('ListPrice', 'PublicRemarks', 'BathroomsFull', 'BedroomsTotal', 'ListingId', 'photos')
    .orderBy('ListPrice', 'desc')
    .limit(24) 
    .countBy('exact')
%}
```

**Methods:** A `RetsRabbit_SearchCriteriaModel` has the following methods available for building a paginated search query.

* forId($searchId) - **(required)** Pass in the search id, usually from the url
* select(...$fields) - Pass in a list of fields you specifically want from the API
* limit($limit) - **(required)** How many results per page
* orderBy($field, $dir) - Order the results 
* countBy($cacheType) - Specify the type of total results query for the API to run. Valid values are either 'exact' or 'estimated'. Uses 'estimated' by default.

#### Complete Example

```html
{% set searchId = craft.request.getSegment(3) %}
    
{% if not craft.retsRabbit.searches.exists(searchId) %}
    {% redirect '404' %}
{% endif %}

{% set criteriaModel = craft.retsRabbit.searches.criteria() %}
{% set criteria = criteriaModel
    .forId(searchId)
    .select('ListPrice', 'PublicRemarks', 'BathroomsFull', 'BedroomsTotal', 'ListingId', 'photos')
    .orderBy('ListPrice', 'desc')
    .limit(24) 
    .countBy('exact')
%}

{% rrPaginate criteria as pageInfo, results %}

{% if results is null %}
    <article class="message is-danger">
        <div class="message-header">
            <p>Uh oh...</p>
        </div>
        <div class="message-body">
            We could not process your request. Please try again.
        </div>
    </article>
{% elseif results|length == 0 %}
    <article class="message is-warning">
        <div class="message-header">
            <p>Hmm..</p>
        </div>
        <div class="message-body">
            We could not find any results for your search. Try changing your search parameters.
        </div>
    </article>
{% else %}
    <div class="columns is-multiline">
        {% for listing in results %}
            <div class="column is-4">
                {% include "properties/includes/_grid-item" %}
            </div>
        {% endfor %}
    </div>
    {% if pageInfo.totalPages > 1 %}
        <nav class="pagination is-centered" role="navigation" aria-label="pagination">
            {% if pageInfo.prevUrl %}
                <a class="pagination-previous" aria-label="Previous page" href="{{pageInfo.prevUrl}}">Previous</a>
            {% endif %}
            {% if pageInfo.nextUrl %}
                <a class="pagination-next" aria-label="Next page" href="{{pageInfo.nextUrl}}">Next page</a>
            {% endif %}

            <ul class="pagination-list">
                {% if pageInfo.currentPage > 2 %}
                    <li>
                        <a href="{{pageInfo.firstUrl}}" class="pagination-link" aria-label="Goto first page">First</a>
                    </li>
                    <li>
                        <span class="pagination-ellipsis">&hellip;</span>
                    </li>
                {% endif %}
                {% for page, url in pageInfo.getPrevUrls(2) %}
                    <li>
                        <a class="pagination-link" aria-label="Goto page {{page}}" href="{{ url }}">{{ page }}</a>
                    </li>
                {% endfor %}
                <li>
                    <a class="pagination-link is-current" aria-label="Page {{pageInfo.currentPage}}" aria-current="page">{{pageInfo.currentPage}}</a>
                </li>
                {% for page, url in pageInfo.getNextUrls(2) %}
                    <li>
                        <a class="pagination-link" href="{{ url }}" aria-label="Goto page {{page}}">{{ page }}</a>
                    </li>
                {% endfor %}
                {% if pageInfo.nextUrl %}
                    <li>
                        <span class="pagination-ellipsis">&hellip;</span>
                    </li>
                    <li>
                        <a href="{{pageInfo.lastUrl}}" class="pagination-link" aria-label="Goto last page">Last</a>
                    </li>
                {% endif %}
            </ul>
        </nav>
    {% endif %}
{% endif %}
```

We used [Bulma.io](https://bulma.io/) in this example, but the above markup will generate something like the following.

![Pagination](screenshots/pagination.png "Pagination")

### Other Variables

Aside from the `PropertiesVariable`, there are a couple of other variables you have access to in your templates.

* SearchesVariable - `craft.retsRabbit.searches`

#### SearchesVariable

This template variable has the following methods:

1. [exists](#bool-existsint-id)

#### *bool* exists(*int* $id)

This method checks if a given search id exists. This method is useful for checking if a search exists before trying to execute it which will provide more predictable error handling.