# IDX API Endpoints
1. [General API Behaviour](#general-api-responses)
2. [`GET /auth/me/json/`](#get-authmejson)
3. [`GET /idx/feed/json/`](#get-idxfeedjson)
4. [`GET /idx/feed/<feed>/panels/json/`](#get-idxfeedfeedpanelsjson)
5. [`GET /idx/feed/<feed>/json/`](#get-idxfeedfeedjson)
6. [`GET /idx/feed/<feed>/count/json/`](#get-idxfeedfeedcountjson)
7. [`GET /idx/feed/<feed>/favorites/json/`](#get-idxfeedfeedfavoritesjson)
8. [`POST /idx/feed/<feed>/favorites/json/`](#post-idxfeedfeedfavoritesjson)
9. [`DELETE /idx/feed/<feed>/favorites/json/`](#delete-idxfeedfeedfavoritesjson)
10. [`POST /idx/feed/<feed>/save/json/`](#post-idxfeedfeedsavejson)
11. [`POST /idx/feed/<feed>/save/<id>/json/`](#post-idxfeedfeedsaveidjson)
12. [`GET /drivetime/json/`](#get-drivetimejson)

## General API responses
If a request goes well, generally a 200 or 204 HTTP code will be sent back in the response.

## Errors
If an error occurs, the API will give an appropriate status code in return, as well as a JSON payload providing some details.

### 500 Internal Errors
If something goes wrong that is not user-error, an `internal_error` type will be returned with a 500 status code.

### Example
```
{"message":"The request failed due to an internal error.","type":"internal_error"}
```

### 422 Unprocessable Entity
If a user provides meaningless/invalid data when making a request to the API, a `validation_error` type will be returned with a 422 status code.

### Example
```
{"message":"The request failed to validate.","type":"validation_error","errors":[{"field":"limit","message":"\"5.1\" is not of required type \"int\"."}]}
```


## `GET /auth/me/json/`
Returns the current users auth token in GUID format, and the current timestamp.

### Parameters:
None!
### Examples:
#### Request:
```
GET http://www.pickles.com:8000/auth/me/json/
```
### Response (200)
```
{
    "token":"F014592F-8852-11E8-B5EF-42010AF010AE",
    "timestamp":1531846489
}
```
## Errors
### 401 Unauthorized
If the user does not have an active session, a 401 is thrown.

## `GET /idx/feed/json/`
Returns a JSON-encoded list of feeds that the site supports, as well as what search fields the feed supports.
### Parameters:
None!
### Examples:
#### Request:
```
GET http://www.pickles.com:8000/idx/feed/json/
```
#### Response (200)
```
[
    {
        "name": "abor",
        "fields": [
            {
                "name": "search_mls",
                "display_name": "MLS Number",
                "type": "string"
            },
            {
                "name": "minimum_price",
                "display_name": "Min. Price",
                "type": "number"
            },
            {
                "name": "maximum_price",
                "display_name": "Max. Price",
                "type": "number"
            },
            {
                "name": "minimum_rent",
                "display_name": "Min. Price",
                "type": "number"
            },
            {
                "name": "maximum_rent",
                "display_name": "Max. Price",
                "type": "number"
            },
            {
                "name": "search_type",
                "display_name": "Property Type",
                "type": "enum",
                "allowed_values": [
                    "Commercial Lease",
                    "Commercial Sale",
                    "Farms & Acreage",
                    "Lot",
                    "Multi-Family",
                    "Rental",
                    "Residential"
                ]
            },
            {
                "name": "search_subtype",
                "display_name": "Property Sub-Type",
                "type": "enum",
                "allowed_values": [
                    "Apartment",
                    "Apartment Complex",
                    "Attached 1/2 Duplex",
                    "Bed & Breakfast",
                    "Condo",
                    "Condo Attached",
                    "Convenience Store",
                    "Duplex",
                    "Efficiency",
                    "Fourplex",
                    "Garage Apartment",
                    "Hotel/Motel",
                    "House",
                    "Indus Bldg-Office/Warehouse",
                    "Indus Bldg-Other See Remarks",
                    "Indus Bldg-Self Storage/Mini Wse",
                    "Indus Bldg-Warehous/R&D",
                    "Indus Business Park Complex",
                    "Land-Agricultural",
                    "Land-Horse Ranch/Farm",
                    "Land-Livestock",
                    "Land-Nursery",
                    "Land-Other Comm/Income",
                    "Land-Row Crop",
                    "Land-Timber",
                    "Manufactured",
                    "Mobile Home",
                    "Mobile Home Park",
                    "Modular",
                    "Multi-Family 5+ Units",
                    "Multi-plex",
                    "Multiple Lots (Adjacent)",
                    "Net Leased Indust/NNN-Single Tnt",
                    "Net Leased Office/NNN-Single Tnt",
                    "Net Leased Retail/NNN-Single Tnt",
                    "None",
                    "Office Bldg",
                    "Office Condo",
                    "Office/Medical Bldg",
                    "Other Comm/Income-See Remarks",
                    "Retail Bldg-Other See Remarks",
                    "Retail Bldg-Restaurant",
                    "Retail Bldg-Shopping/Strip Centr",
                    "See Agent",
                    "Single Lot",
                    "Site-Hotel/Motel",
                    "Site-Industrial",
                    "Site-Mixed Use",
                    "Site-Multi-Family 5+ Units",
                    "Site-Office",
                    "Site-Other See Remarks",
                    "Site-Pad",
                    "Site-Retail",
                    "Site-Single Family Development",
                    "Townhouse",
                    "Triplex"
                ]
            },
            {
                "name": "search_status",
                "display_name": "Status",
                "type": "string"
            },
            {
                "name": "minimum_dom",
                "display_name": "Min. Days on Market",
                "type": "number"
            },
            {
                "name": "maximum_dom",
                "display_name": "Max. Days on Market",
                "type": "number"
            },
            {
                "name": "search_address",
                "display_name": "Address",
                "type": "string"
            },
            {
                "name": "search_area",
                "display_name": "Area",
                "type": "string"
            },
            {
                "name": "search_subdivision",
                "display_name": "Subdivision",
                "type": "string"
            },
            {
                "name": "search_city",
                "display_name": "City",
                "type": "string"
            },
            {
                "name": "search_county",
                "display_name": "County",
                "type": "string"
            },
            {
                "name": "search_state",
                "display_name": "State",
                "type": "string"
            },
            {
                "name": "search_zip",
                "display_name": "Zip Code",
                "type": "string"
            },
            {
                "name": "school_district",
                "display_name": "School District",
                "type": "string"
            },
            {
                "name": "school_elementary",
                "display_name": "Elementary School",
                "type": "string"
            },
            {
                "name": "school_middle",
                "display_name": "Middle School",
                "type": "string"
            },
            {
                "name": "school_high",
                "display_name": "High School",
                "type": "string"
            },
            {
                "name": "minimum_beds",
                "display_name": "Min. Bedrooms",
                "type": "number"
            },
            {
                "name": "minimum_bedrooms",
                "display_name": "Min. Bedrooms",
                "type": "number"
            },
            {
                "name": "maximum_bedrooms",
                "display_name": "Max. Bedrooms",
                "type": "number"
            },
            {
                "name": "minimum_baths",
                "display_name": "Min. Bathrooms",
                "type": "number"
            },
            {
                "name": "minimum_bathrooms",
                "display_name": "Min. Bathrooms",
                "type": "number"
            },
            {
                "name": "maximum_bathrooms",
                "display_name": "Max. Bathrooms",
                "type": "number"
            },
            {
                "name": "minimum_sqft",
                "display_name": "Min. Sq. Ft.",
                "type": "number"
            },
            {
                "name": "maximum_sqft",
                "display_name": "Max. Sq. Ft.",
                "type": "number"
            },
            {
                "name": "minimum_acres",
                "display_name": "Min. Acres",
                "type": "number"
            },
            {
                "name": "maximum_acres",
                "display_name": "Max. Acres",
                "type": "number"
            },
            {
                "name": "minimum_year",
                "display_name": "Min. Year Built",
                "type": "number"
            },
            {
                "name": "maximum_year",
                "display_name": "Max. Year Built",
                "type": "number"
            },
            {
                "name": "search_pool",
                "display_name": "Swimming Pool",
                "type": "enum"
            },
            {
                "name": "search_fireplace",
                "display_name": "Has Fireplace",
                "type": "enum"
            },
            {
                "name": "search_waterfront",
                "display_name": "Waterfront",
                "type": "enum"
            },
            {
                "name": "search_foreclosure",
                "display_name": "Foreclosure",
                "type": "enum"
            },
            {
                "name": "search_shortsale",
                "display_name": "Short Sale",
                "type": "enum"
            },
            {
                "name": "search_bankowned",
                "display_name": "Bank Owned",
                "type": "enum"
            },
            {
                "name": "search_office",
                "display_name": "Office Name",
                "type": "string"
            },
            {
                "name": "office_id",
                "display_name": "Office ID",
                "type": "string"
            },
            {
                "name": "search_agent",
                "display_name": "Agent Name",
                "type": "string"
            },
            {
                "name": "agent_id",
                "display_name": "Agent ID",
                "type": "string"
            }
        ]
    }
]
```

#### Request:
```
GET http://www.pickles.com:8000/idx/feed/json/
```
#### Response (200)
```
[
    {
        "name": "rebny",
        "fields": [
            {
                "name": "search_mls",
                "display_name": "MLS Number",
                "type": "string"
            },
            {
                "name": "minimum_price",
                "display_name": "Min. Price",
                "type": "number"
            },
            {
                "name": "maximum_price",
                "display_name": "Max. Price",
                "type": "number"
            },
            {
                "name": "minimum_rent",
                "display_name": "Min. Price",
                "type": "number"
            },
            {
                "name": "maximum_rent",
                "display_name": "Max. Price",
                "type": "number"
            },
            {
                "name": "search_type",
                "display_name": "Property Type",
                "type": "enum",
                "allowed_values": [
                    "BuildingRent",
                    "BuildingSale",
                    "UnitRent",
                    "UnitSale"
                ]
            },
            {
                "name": "search_subtype",
                "display_name": "Property Sub-Type",
                "type": "enum",
                "allowed_values": [
                    "CondoCoop",
                    "Apartment",
                    "Single Family",
                    "Other",
                    "Multi-family",
                    "Townhouse",
                    "Commercial and Residential",
                    "Commercial"
                ]
            },
            {
                "name": "search_status",
                "display_name": "Status",
                "type": "string"
            },
            {
                "name": "minimum_dom",
                "display_name": "Min. Days on Market",
                "type": "number"
            },
            {
                "name": "maximum_dom",
                "display_name": "Max. Days on Market",
                "type": "number"
            },
            {
                "name": "search_address",
                "display_name": "Address",
                "type": "string"
            },
            {
                "name": "search_area",
                "display_name": "Area",
                "type": "string"
            },
            {
                "name": "search_subdivision",
                "display_name": "Subdivision",
                "type": "string"
            },
            {
                "name": "search_city",
                "display_name": "City",
                "type": "string"
            },
            {
                "name": "search_county",
                "display_name": "County",
                "type": "string"
            },
            {
                "name": "search_state",
                "display_name": "State",
                "type": "string"
            },
            {
                "name": "search_zip",
                "display_name": "Zip Code",
                "type": "string"
            },
            {
                "name": "school_district",
                "display_name": "School District",
                "type": "string"
            },
            {
                "name": "school_elementary",
                "display_name": "Elementary School",
                "type": "string"
            },
            {
                "name": "school_middle",
                "display_name": "Middle School",
                "type": "string"
            },
            {
                "name": "school_high",
                "display_name": "High School",
                "type": "string"
            },
            {
                "name": "minimum_beds",
                "display_name": "Min. Bedrooms",
                "type": "number"
            },
            {
                "name": "minimum_bedrooms",
                "display_name": "Min. Bedrooms",
                "type": "number"
            },
            {
                "name": "maximum_bedrooms",
                "display_name": "Max. Bedrooms",
                "type": "number"
            },
            {
                "name": "minimum_baths",
                "display_name": "Min. Bathrooms",
                "type": "number"
            },
            {
                "name": "minimum_bathrooms",
                "display_name": "Min. Bathrooms",
                "type": "number"
            },
            {
                "name": "maximum_bathrooms",
                "display_name": "Max. Bathrooms",
                "type": "number"
            },
            {
                "name": "minimum_sqft",
                "display_name": "Min. Sq. Ft.",
                "type": "number"
            },
            {
                "name": "maximum_sqft",
                "display_name": "Max. Sq. Ft.",
                "type": "number"
            },
            {
                "name": "minimum_acres",
                "display_name": "Min. Acres",
                "type": "number"
            },
            {
                "name": "maximum_acres",
                "display_name": "Max. Acres",
                "type": "number"
            },
            {
                "name": "minimum_year",
                "display_name": "Min. Year Built",
                "type": "number"
            },
            {
                "name": "maximum_year",
                "display_name": "Max. Year Built",
                "type": "number"
            },
            {
                "name": "search_pool",
                "display_name": "Swimming Pool",
                "type": "enum"
            },
            {
                "name": "search_fireplace",
                "display_name": "Has Fireplace",
                "type": "enum"
            },
            {
                "name": "search_waterfront",
                "display_name": "Waterfront",
                "type": "enum"
            },
            {
                "name": "search_foreclosure",
                "display_name": "Foreclosure",
                "type": "enum"
            },
            {
                "name": "search_shortsale",
                "display_name": "Short Sale",
                "type": "enum"
            },
            {
                "name": "search_bankowned",
                "display_name": "Bank Owned",
                "type": "enum"
            },
            {
                "name": "search_office",
                "display_name": "Office Name",
                "type": "string"
            },
            {
                "name": "office_id",
                "display_name": "Office ID",
                "type": "string"
            },
            {
                "name": "search_agent",
                "display_name": "Agent Name",
                "type": "string"
            },
            {
                "name": "agent_id",
                "display_name": "Agent ID",
                "type": "string"
            }
        ]
    },
    {
        "name": "carets",
        "fields": [
            {
                "name": "search_mls",
                "display_name": "MLS Number",
                "type": "string"
            },
            {
                "name": "minimum_price",
                "display_name": "Min. Price",
                "type": "number"
            },
            {
                "name": "maximum_price",
                "display_name": "Max. Price",
                "type": "number"
            },
            {
                "name": "minimum_rent",
                "display_name": "Min. Price",
                "type": "number"
            },
            {
                "name": "maximum_rent",
                "display_name": "Max. Price",
                "type": "number"
            },
            {
                "name": "search_type",
                "display_name": "Property Type",
                "type": "enum",
                "allowed_values": [
                    "Lot-Land",
                    "Residential Income",
                    "Rental",
                    "Residential",
                    "Mobile Home"
                ]
            },
            {
                "name": "search_subtype",
                "display_name": "Property Sub-Type",
                "type": "enum",
                "allowed_values": [
                    "Condominium",
                    "Single Family",
                    "Apartment Building",
                    "Townhouse",
                    "Other (L)",
                    "Combo - Res & Com",
                    "Fourplex",
                    "Triplex",
                    "Mobile Home on Land",
                    "Manufactured 433",
                    "Duplex",
                    "OwnYourOwn",
                    "Apartment",
                    "Stock Cooperative",
                    "Loft",
                    "Manufactured Home on Real Property"
                ]
            },
            {
                "name": "search_status",
                "display_name": "Status",
                "type": "string"
            },
            {
                "name": "minimum_dom",
                "display_name": "Min. Days on Market",
                "type": "number"
            },
            {
                "name": "maximum_dom",
                "display_name": "Max. Days on Market",
                "type": "number"
            },
            {
                "name": "search_address",
                "display_name": "Address",
                "type": "string"
            },
            {
                "name": "search_area",
                "display_name": "Area",
                "type": "string"
            },
            {
                "name": "search_subdivision",
                "display_name": "Subdivision",
                "type": "string"
            },
            {
                "name": "search_city",
                "display_name": "City",
                "type": "string"
            },
            {
                "name": "search_county",
                "display_name": "County",
                "type": "string"
            },
            {
                "name": "search_state",
                "display_name": "State",
                "type": "string"
            },
            {
                "name": "search_zip",
                "display_name": "Zip Code",
                "type": "string"
            },
            {
                "name": "school_district",
                "display_name": "School District",
                "type": "string"
            },
            {
                "name": "school_elementary",
                "display_name": "Elementary School",
                "type": "string"
            },
            {
                "name": "school_middle",
                "display_name": "Middle School",
                "type": "string"
            },
            {
                "name": "school_high",
                "display_name": "High School",
                "type": "string"
            },
            {
                "name": "minimum_beds",
                "display_name": "Min. Bedrooms",
                "type": "number"
            },
            {
                "name": "minimum_bedrooms",
                "display_name": "Min. Bedrooms",
                "type": "number"
            },
            {
                "name": "maximum_bedrooms",
                "display_name": "Max. Bedrooms",
                "type": "number"
            },
            {
                "name": "minimum_baths",
                "display_name": "Min. Bathrooms",
                "type": "number"
            },
            {
                "name": "minimum_bathrooms",
                "display_name": "Min. Bathrooms",
                "type": "number"
            },
            {
                "name": "maximum_bathrooms",
                "display_name": "Max. Bathrooms",
                "type": "number"
            },
            {
                "name": "minimum_sqft",
                "display_name": "Min. Sq. Ft.",
                "type": "number"
            },
            {
                "name": "maximum_sqft",
                "display_name": "Max. Sq. Ft.",
                "type": "number"
            },
            {
                "name": "minimum_acres",
                "display_name": "Min. Acres",
                "type": "number"
            },
            {
                "name": "maximum_acres",
                "display_name": "Max. Acres",
                "type": "number"
            },
            {
                "name": "minimum_year",
                "display_name": "Min. Year Built",
                "type": "number"
            },
            {
                "name": "maximum_year",
                "display_name": "Max. Year Built",
                "type": "number"
            },
            {
                "name": "search_pool",
                "display_name": "Swimming Pool",
                "type": "enum"
            },
            {
                "name": "search_fireplace",
                "display_name": "Has Fireplace",
                "type": "enum"
            },
            {
                "name": "search_waterfront",
                "display_name": "Waterfront",
                "type": "enum"
            },
            {
                "name": "search_foreclosure",
                "display_name": "Foreclosure",
                "type": "enum"
            },
            {
                "name": "search_shortsale",
                "display_name": "Short Sale",
                "type": "enum"
            },
            {
                "name": "search_bankowned",
                "display_name": "Bank Owned",
                "type": "enum"
            },
            {
                "name": "search_office",
                "display_name": "Office Name",
                "type": "string"
            },
            {
                "name": "office_id",
                "display_name": "Office ID",
                "type": "string"
            },
            {
                "name": "search_agent",
                "display_name": "Agent Name",
                "type": "string"
            },
            {
                "name": "agent_id",
                "display_name": "Agent ID",
                "type": "string"
            }
        ]
    }
]
```

## `GET /idx/feed/<feed>/panels/json/`
Returns a JSON-encoded object of panels for a given feed. If the feed doesn't exist, the default panels will be returned.

### Parameters:
None!

### Examples:
#### Request:
```
GET http://www.example.com/idx/feed/mfr/panels/json/
```

#### Response (200)
``` 
{
    "type": {
        "title": "Property Type",
        "param_name": "search_type",
        "type": "checklist",
        "hidden": false,
        "collapsed": false,
        "display": true,
        "options": [
            {
                "value": "",
                "title": "All Properties"
            },
            {
                "value": "Residential",
                "title": "Residential"
            },
            {
                "value": "Land",
                "title": "Land"
            },
            {
                "value": "Rental",
                "title": "Rental"
            },
            {
                "value": "Commercial",
                "title": "Commercial"
            },
            {
                "value": "Income",
                "title": "Income"
            }
        ]
    },
    "city": {
        "title": "City",
        "param_name": "search_city",
        "type": "checklist",
        "hidden": false,
        "collapsed": false,
        "display": true,
        "options": [
            {
                "value": "Altamonte Spg",
                "title": "Altamonte Spg"
            },
            {
                "value": "Altamonte Springs",
                "title": "Altamonte Springs"
            },
            {
                "value": "Altoona",
                "title": "Altoona"
            }
    }
}
```


## `GET /idx/feed/<feed>/json/`
Takes in a set of criteria and returns a paginated, JSON-encoded list of IDX listings matching that criteria.

### Parameters:
- `after` (string) The base64-encoded cursor to the next page of listing results.
- `before` (string) The base64-encoded cursor to the previous page of listing results.
- `limit` (int) The number of listings that should be returned per-page.
- `order` (string) The field to sort the listing results by.
- `sort` (string) The direction to sort the results by.
    - Valid inputs: `ASC`, `DESC`
- `criteria` (JSON) The criteria that the results should adhere to:
    - `bounds` (JSON object) The corners of a map to bound the results by.
        - `ne` (JSON object) The NorthEast corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
        - `sw` (JSON object) The SouthWest corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
    - `radius` (JSON array) The coordinates and radius unit the desired listings should be in.
        - (JSON array element) There can be multiple radii in the radius array.
            - `lat` (float) Latitude of origin coordinate.
            - `lng` (float) Longitude of origin coordinate.
            - `radius` (float) Size of radius (in miles).
    - `polygon` (JSON array) The polygons that the desired listings should be in. There can be multiple polygons, 
    all of which should follow the [Well-Known Text Polygon Format](http://geoscript.org/examples/geom/wkt.html).

### Examples:
#### Request:
```
GET http://www.pickles.com:8000/idx/feed/abor/json/?sort=ASC&order=ListingMLS&criteria={"property_type":"Residential"}
```

#### Response (200)
```
{
    "limit": 10,
    "before": null,
    "after": "http://www.pickles.com:8000/idx/feed/abor/json/?after=Y29sdW1uPUxpc3RpbmdNTFMmY291bnQ9OSZvcmRlciU1QjAlNUQ9TGlzdGluZ01MUyZzb3J0JTVCMCU1RD1BU0MmbGFzdCU1QjAlNUQ9MTAwNjU3OSZpZD0xMDA2NTc5",
    "listingResults": [
        {
            "url": "http://www.pickles.com:8000/listing/1000062-800-brazos-st-902-austin/",
            "photo": "https://276a274275c19791e344-2d988a147a318d18c687152f92385d38.ssl.cf5.rackcdn.com/1000062-residential-epbu4e-l.jpg",
            "address": "800 Brazos St #902",
            "city": "Austin",
            "currency": "$",
            "listPrice": 324900,
            "propertyType": "Residential",
            "bedrooms": 1,
            "bathrooms": 1,
            "lotSize": 802,
            "lotUnit": "SqFt",
            "Id": "1000062"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1000778-na-austin/",
            "photo": "https://75759da8c4d9e5b2ce3d-61331845db3fedcc433675e2728b7bd6.ssl.cf5.rackcdn.com/1000778-residential-ngrzmg-l.jpg",
            "address": "",
            "city": "Austin",
            "currency": "$",
            "listPrice": 274990,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 2,
            "lotSize": 1181,
            "lotUnit": "SqFt",
            "Id": "1000778"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1000784-4303-jm-cuba-taylor/",
            "photo": "https://63f2b1ac09e7deb70a3a-8be2223b7e80ecf05898b77b0c5a18c0.ssl.cf5.rackcdn.com/1000784-residential-13smx8r-l.jpg",
            "address": "4303 Jm Cuba",
            "city": "Taylor",
            "currency": "$",
            "listPrice": 299000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 3,
            "lotSize": 2445,
            "lotUnit": "SqFt",
            "Id": "1000784"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1002476-6605-casimir-cv-austin/",
            "photo": "https://278a10269c930e03a41b-5ae97641c1b1f4a7efec2cf6a4c00686.ssl.cf5.rackcdn.com/1002476-residential-lfbhg3-l.jpg",
            "address": "6605 Casimir Cv",
            "city": "Austin",
            "currency": "$",
            "listPrice": 575000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 3794,
            "lotUnit": "SqFt",
            "Id": "1002476"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1002785-401-riley-trl-cedar-park/",
            "photo": "https://342908a77422c7fddafc-93717541369a7f313af1f6342c88a8b7.ssl.cf5.rackcdn.com/1002785-residential-1jsx55j-l.jpg",
            "address": "401 Riley Trl",
            "city": "Cedar Park",
            "currency": "$",
            "listPrice": 575000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 3883,
            "lotUnit": "SqFt",
            "Id": "1002785"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1004774-121-oak-trail-dr-georgetown/",
            "photo": "https://75759da8c4d9e5b2ce3d-61331845db3fedcc433675e2728b7bd6.ssl.cf5.rackcdn.com/1004774-residential-c1pg18-l.jpg",
            "address": "121 Oak Trail Dr",
            "city": "Georgetown",
            "currency": "$",
            "listPrice": 490000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 2280,
            "lotUnit": "SqFt",
            "Id": "1004774"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1005372-4013-trailview-mesa-dr-austin/",
            "photo": "https://5a069bee7303ea36474c-f03bb4e9c3ff92a72e986b096b0f560e.ssl.cf5.rackcdn.com/1005372-residential-1js9agl-l.jpg",
            "address": "4013 TRAILVIEW MESA Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1675000,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 4,
            "lotSize": 5215,
            "lotUnit": "SqFt",
            "Id": "1005372"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1006268-210-right-ln-horseshoe-bay/",
            "photo": "https://2c819fbc3f13f2c78095-b5c3d4a3e11f163c0da55f7332c6ef53.ssl.cf5.rackcdn.com/1006268-residential-d157pn-l.jpg",
            "address": "210 Right Ln",
            "city": "Horseshoe Bay",
            "currency": "$",
            "listPrice": 749000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 3312,
            "lotUnit": "SqFt",
            "Id": "1006268"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1006579-15132-donna-jane-loop-pflugerville/",
            "photo": "https://d79133ad4c658d4fb1f9-e82224d463dd5675ebd2153f0c1c0c0f.ssl.cf5.rackcdn.com/1006579-residential-1v14p92-l.jpg",
            "address": "15132 Donna Jane Loop",
            "city": "Pflugerville",
            "currency": "$",
            "listPrice": 234000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 1860,
            "lotUnit": "SqFt",
            "Id": "1006579"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1007671-504-obsidian-ln-jarrell/",
            "photo": "https://94aaafba42992c0c09aa-3ad01d2e174f9c778afa95cf3f4e5dee.ssl.cf5.rackcdn.com/1007671-residential-1qdcve7-l.jpg",
            "address": "504 Obsidian Ln",
            "city": "Jarrell",
            "currency": "$",
            "listPrice": 200000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 2094,
            "lotUnit": "SqFt",
            "Id": "1007671"
        }
    ]
}

```
#### Request:
```
GET http://www.pickles.com:8000/idx/feed/abor/json/?sort=ASC&order=ListingMLS&criteria={"property_type":"Residential", "min_price":1000000, "max_price":1200000}
```
#### Response (200)
```
{
    "limit": 10,
    "before": null,
    "after": "http://www.pickles.com:8000/idx/feed/abor/json/?after=Y29sdW1uPUxpc3RpbmdNTFMmY291bnQ9OSZvcmRlciU1QjAlNUQ9TGlzdGluZ01MUyZzb3J0JTVCMCU1RD1BU0MmbGFzdCU1QjAlNUQ9MTUwODA2MCZpZD0xNTA4MDYw",
    "listingResults": [
        {
            "url": "http://www.pickles.com:8000/listing/1094775-300-bowie-st-2901-austin/",
            "photo": "https://7ff26cb8b8fd6b68c0e1-da429c3d1237d0a2ca07d7955f079639.ssl.cf5.rackcdn.com/1094775-residential-w8a87g-l.jpg",
            "address": "300 Bowie St #2901",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1099000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 1631,
            "lotUnit": "SqFt",
            "Id": "1094775"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1133125-1501-mesa-ridge-ln-austin/",
            "photo": "https://94aaafba42992c0c09aa-3ad01d2e174f9c778afa95cf3f4e5dee.ssl.cf5.rackcdn.com/1133125-residential-m1w1eq-l.jpg",
            "address": "1501 Mesa Ridge Ln",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1099950,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 5,
            "lotSize": 5162,
            "lotUnit": "SqFt",
            "Id": "1133125"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1138003-312-dolcetto-ct-lakeway/",
            "photo": "https://d79133ad4c658d4fb1f9-e82224d463dd5675ebd2153f0c1c0c0f.ssl.cf5.rackcdn.com/1138003-residential-19rcnl5-l.jpg",
            "address": "312 Dolcetto Ct",
            "city": "Lakeway",
            "currency": "$",
            "listPrice": 1049000,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 5,
            "lotSize": 4408,
            "lotUnit": "SqFt",
            "Id": "1138003"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1173341-9009-thickwoods-cv-austin/",
            "photo": "https://d7a3dadab831a46736c9-1af22494b60431c84b44c237ddf7e647.ssl.cf5.rackcdn.com/1173341-residential-1rpvuhq-l.jpg",
            "address": "9009 Thickwoods Cv",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1150000,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 4,
            "lotSize": 4100,
            "lotUnit": "SqFt",
            "Id": "1173341"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1232268-2901-s-old-stagecoach-rd-kyle/",
            "photo": "https://41340d5c0875952c9882-628eddfbf836917dd7cf9b513dd49e30.ssl.cf5.rackcdn.com/1232268-residential-jxpwfn-l.jpg",
            "address": "2901 S Old Stagecoach Rd",
            "city": "Kyle",
            "currency": "$",
            "listPrice": 1145000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 3772,
            "lotUnit": "SqFt",
            "Id": "1232268"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1246802-2015-shallow-stream-cv-austin/",
            "photo": "https://d776ecb0dd22c2038d21-31d298ac8759087d056dff3eefc47860.ssl.cf5.rackcdn.com/1246802-residential-a1jxz3-l.jpg",
            "address": "2015 Shallow Stream Cv",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1150000,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 6,
            "lotSize": 4182,
            "lotUnit": "SqFt",
            "Id": "1246802"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1296741-1300-gulf-blvd-1204-other/",
            "photo": "https://41340d5c0875952c9882-628eddfbf836917dd7cf9b513dd49e30.ssl.cf5.rackcdn.com/1296741-residential-8h9eet-l.jpg",
            "address": "1300 Gulf Blvd #1204",
            "city": "Other",
            "currency": "$",
            "listPrice": 1050000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 2191,
            "lotUnit": "SqFt",
            "Id": "1296741"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1414750-3309-scenic-overlook-trl-austin/",
            "photo": "https://d033392793fe91cb02ef-eff2bdda8689f698dd1e6214837ebfbb.ssl.cf5.rackcdn.com/1414750-residential-1xa2hri-l.jpg",
            "address": "3309 Scenic Overlook Trl",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1015000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 5,
            "lotSize": 4608,
            "lotUnit": "SqFt",
            "Id": "1414750"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1508060-na-salado/",
            "photo": "https://94aaafba42992c0c09aa-3ad01d2e174f9c778afa95cf3f4e5dee.ssl.cf5.rackcdn.com/1508060-residential-1giwyzc-l.jpg",
            "address": "",
            "city": "Salado",
            "currency": "$",
            "listPrice": 1160000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 4162,
            "lotUnit": "SqFt",
            "Id": "1508060"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1528407-18309-flagler-dr-austin/",
            "photo": "https://0113f87417d8d5bf8018-fac084ba267067c705c4804439c911dd.ssl.cf5.rackcdn.com/1528407-residential-1cccjpl-l.jpg",
            "address": "18309 Flagler Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1200000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 6,
            "lotSize": 4173,
            "lotUnit": "SqFt",
            "Id": "1528407"
        }
    ]
}
```
### Request
```
http://www.pickles.com:8000/idx/feed/abor/json/?criteria={"bounds":{"ne":{"lat":30.400622, "lng": -97.643048}, "sw":{"lat":30.396476, "lng":-97.653949}}}
```
### Response (200)
```
{
    "limit": 4,
    "before": null,
    "after": null,
    "listingResults": [
        {
            "url": "http://www.pickles.com:8000/listing/4593273-12706-blaine-rd-austin/",
            "photo": "https://4aa3ae4cc1ca24b64586-dafda21b792af8c00fe29aeb4465fc90.ssl.cf5.rackcdn.com/4593273-rental-1wf7ybt-l.jpg",
            "address": "12706 Blaine Rd",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1677,
            "propertyType": "Rental",
            "bedrooms": 3,
            "bathrooms": 2,
            "lotSize": 1643,
            "lotUnit": "SqFt",
            "id": "4593273"
        },
        {
            "url": "http://www.pickles.com:8000/listing/8320057-1321-dexford-dr-austin/",
            "photo": "https://15f52b91b5a626bf7664-62ab636a7a946b03665a5dfa04e69aca.ssl.cf5.rackcdn.com/8320057-residential-zrc1t4-l.jpg",
            "address": "1321 Dexford Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 229900,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 2,
            "lotSize": 1522,
            "lotUnit": "SqFt",
            "id": "8320057"
        },
        {
            "url": "http://www.pickles.com:8000/listing/8630717-1433-dexford-dr-austin/",
            "photo": "https://cddbe67a002445e9adb2-1942d925a3909292a3fba3495fa0ecee.ssl.cf5.rackcdn.com/8630717-residential-5xuwoa-l.jpg",
            "address": "1433 Dexford Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 239900,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 2,
            "lotSize": 1682,
            "lotUnit": "SqFt",
            "id": "8630717"
        },
        {
            "url": "http://www.pickles.com:8000/listing/9014227-1101-dexford-dr-austin/",
            "photo": "https://d40b4a3e1b7b4c471ebf-efedb2aa616427ef198364660879f78d.ssl.cf5.rackcdn.com/9014227-residential-heakid-l.jpg",
            "address": "1101 Dexford Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 275000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 3,
            "lotSize": 3302,
            "lotUnit": "SqFt",
            "id": "9014227"
        }
    ]
}
```
### Request
```
http://www.pickles.com:8000/idx/feed/abor/json/?criteria={"radius":[{"lat":30.370433, "lng": -97.773330, "radius":950}]}
```
### Response (200)
```
{
    "limit": 19,
    "before": null,
    "after": null,
    "listingResults": [
        {
            "url": "http://www.pickles.com:8000/listing/2050389-6706-fort-davis-cv-a-austin/",
            "photo": "https://4aa3ae4cc1ca24b64586-dafda21b792af8c00fe29aeb4465fc90.ssl.cf5.rackcdn.com/2050389-rental-2mn64f-l.jpg",
            "address": "6706 Fort Davis Cv #A",
            "city": "Austin",
            "currency": "$",
            "listPrice": 2395,
            "propertyType": "Rental",
            "bedrooms": 2,
            "bathrooms": 2,
            "lotSize": 1600,
            "lotUnit": "SqFt",
            "id": "2050389"
        },
        {
            "url": "http://www.pickles.com:8000/listing/6378324-na-austin/",
            "photo": "https://4aa3ae4cc1ca24b64586-dafda21b792af8c00fe29aeb4465fc90.ssl.cf5.rackcdn.com/6378324-rental-1ff8jd7-l.jpg",
            "address": "",
            "city": "Austin",
            "currency": "$",
            "listPrice": 3175,
            "propertyType": "Rental",
            "bedrooms": 4,
            "bathrooms": 2,
            "lotSize": 2272,
            "lotUnit": "SqFt",
            "id": "6378324"
        },
        {
            "url": "http://www.pickles.com:8000/listing/9238105-4206-lostridge-dr-austin/",
            "photo": "https://fbff219294f1c00fa2ac-587f7bb9336f686e32f3e8c306cdeeda.ssl.cf5.rackcdn.com/9238105-residential-1hd557g-l.jpg",
            "address": "4206 Lostridge Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 624900,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 2,
            "lotSize": 2228,
            "lotUnit": "SqFt",
            "id": "9238105"
        },
        {
            "url": "http://www.pickles.com:8000/listing/2871673-4103-greystone-dr-austin/",
            "photo": "https://15f52b91b5a626bf7664-62ab636a7a946b03665a5dfa04e69aca.ssl.cf5.rackcdn.com/2871673-residential-1afplm7-l.jpg",
            "address": "4103 Greystone Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 629000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 2,
            "lotSize": 2214,
            "lotUnit": "SqFt",
            "id": "2871673"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1338367-6815-cougar-run-austin/",
            "photo": "https://7e141ee576a20022ca38-f2a6e5cce4547156ccf986cc7d02f86a.ssl.cf5.rackcdn.com/1338367-residential-jdisjc-l.jpg",
            "address": "6815 Cougar Run",
            "city": "Austin",
            "currency": "$",
            "listPrice": 694000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 2625,
            "lotUnit": "SqFt",
            "id": "1338367"
        },
        {
            "url": "http://www.pickles.com:8000/listing/6173224-5129-valburn-ct-austin/",
            "photo": "https://dbc46b8802f15672bd6e-80f4fb62abaf0896eb3af01b6bebe9d6.ssl.cf5.rackcdn.com/6173224-residential-1rtc3u6-l.jpg",
            "address": "5129 Valburn Ct",
            "city": "Austin",
            "currency": "$",
            "listPrice": 712000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 2492,
            "lotUnit": "SqFt",
            "id": "6173224"
        },
        {
            "url": "http://www.pickles.com:8000/listing/3302584-4203-gnarl-dr-austin/",
            "photo": "https://13c46618f4407fb7fbad-a75d55cbe61258c2a1e54f59198ac4c1.ssl.cf5.rackcdn.com/3302584-residential-1h3rj6q-l.jpg",
            "address": "4203 Gnarl Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 749000,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 4,
            "lotSize": 3263,
            "lotUnit": "SqFt",
            "id": "3302584"
        },
        {
            "url": "http://www.pickles.com:8000/listing/2217390-4825-spicewood-springs-rd-austin/",
            "photo": "https://15f52b91b5a626bf7664-62ab636a7a946b03665a5dfa04e69aca.ssl.cf5.rackcdn.com/2217390-residential-idxv89-l.jpg",
            "address": "4825 Spicewood Springs Rd",
            "city": "Austin",
            "currency": "$",
            "listPrice": 750000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 2439,
            "lotUnit": "SqFt",
            "id": "2217390"
        },
        {
            "url": "http://www.pickles.com:8000/listing/3323648-7503-long-point-dr-austin/",
            "photo": "https://d033392793fe91cb02ef-eff2bdda8689f698dd1e6214837ebfbb.ssl.cf5.rackcdn.com/3323648-residential-1tpq3yk-l.jpg",
            "address": "7503 Long Point Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 779000,
            "propertyType": "Residential",
            "bedrooms": 5,
            "bathrooms": 3,
            "lotSize": 2992,
            "lotUnit": "SqFt",
            "id": "3323648"
        },
        {
            "url": "http://www.pickles.com:8000/listing/2754269-7604-west-rim-dr-austin/",
            "photo": "https://d033392793fe91cb02ef-eff2bdda8689f698dd1e6214837ebfbb.ssl.cf5.rackcdn.com/2754269-residential-y0d00l-l.jpg",
            "address": "7604 West Rim Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 799000,
            "propertyType": "Residential",
            "bedrooms": 3,
            "bathrooms": 3,
            "lotSize": 2431,
            "lotUnit": "SqFt",
            "id": "2754269"
        },
        {
            "url": "http://www.pickles.com:8000/listing/5958132-4220-lostridge-dr-austin/",
            "photo": "https://94aaafba42992c0c09aa-3ad01d2e174f9c778afa95cf3f4e5dee.ssl.cf5.rackcdn.com/5958132-residential-onubwt-l.jpg",
            "address": "4220 Lostridge Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 835000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 3,
            "lotSize": 2407,
            "lotUnit": "SqFt",
            "id": "5958132"
        },
        {
            "url": "http://www.pickles.com:8000/listing/6452575-4902-n-rim-dr-austin/",
            "photo": "https://5a069bee7303ea36474c-f03bb4e9c3ff92a72e986b096b0f560e.ssl.cf5.rackcdn.com/6452575-residential-jv9y51-l.jpg",
            "address": "4902 N Rim Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 849500,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 3723,
            "lotUnit": "SqFt",
            "id": "6452575"
        },
        {
            "url": "http://www.pickles.com:8000/listing/9049537-4300-burney-dr-austin/",
            "photo": "https://94aaafba42992c0c09aa-3ad01d2e174f9c778afa95cf3f4e5dee.ssl.cf5.rackcdn.com/9049537-residential-41nyp0-l.jpg",
            "address": "4300 Burney Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 860000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 3,
            "lotSize": 2500,
            "lotUnit": "SqFt",
            "id": "9049537"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1034900-6905-ladera-norte-austin/",
            "photo": "https://e7a2e1d00533f4686bec-2124ad16a84bed13400447114b63ceb6.ssl.cf5.rackcdn.com/1034900-residential-1lqzqy3-l.jpg",
            "address": "6905 Ladera Norte",
            "city": "Austin",
            "currency": "$",
            "listPrice": 899000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 5,
            "lotSize": 4204,
            "lotUnit": "SqFt",
            "id": "1034900"
        },
        {
            "url": "http://www.pickles.com:8000/listing/1876930-5000-n-rim-dr-austin/",
            "photo": "https://d40b4a3e1b7b4c471ebf-efedb2aa616427ef198364660879f78d.ssl.cf5.rackcdn.com/1876930-residential-16wpf0o-l.jpg",
            "address": "5000 N Rim Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 924000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 3,
            "lotSize": 3448,
            "lotUnit": "SqFt",
            "id": "1876930"
        },
        {
            "url": "http://www.pickles.com:8000/listing/6032240-7205-valburn-dr-austin/",
            "photo": "https://d7a3dadab831a46736c9-1af22494b60431c84b44c237ddf7e647.ssl.cf5.rackcdn.com/6032240-residential-745lqa-l.jpg",
            "address": "7205 VALBURN Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1225000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 3331,
            "lotUnit": "SqFt",
            "id": "6032240"
        },
        {
            "url": "http://www.pickles.com:8000/listing/9996066-7304-valburn-dr-austin/",
            "photo": "https://dbc46b8802f15672bd6e-80f4fb62abaf0896eb3af01b6bebe9d6.ssl.cf5.rackcdn.com/9996066-residential-1bb9l7o-l.jpg",
            "address": "7304 Valburn Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1300000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 3406,
            "lotUnit": "SqFt",
            "id": "9996066"
        },
        {
            "url": "http://www.pickles.com:8000/listing/4788610-4326-palladio-dr-austin/",
            "photo": "https://028ea5e972a0aadde8b8-b542185bed5f61d4d326c91f33214235.ssl.cf5.rackcdn.com/4788610-residential-16su8wi-l.jpg",
            "address": "4326 Palladio Dr",
            "city": "Austin",
            "currency": "$",
            "listPrice": 1375000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 4,
            "lotSize": 4651,
            "lotUnit": "SqFt",
            "id": "4788610"
        },
        {
            "url": "http://www.pickles.com:8000/listing/9522998-5208-rico-cv-austin/",
            "photo": "https://590e905c5e93eae79573-8592ab668d3c0a2de602f64b5aadb738.ssl.cf5.rackcdn.com/9522998-residential-fhhtyh-l.jpg",
            "address": "5208 Rico Cv",
            "city": "Austin",
            "currency": "$",
            "listPrice": 2750000,
            "propertyType": "Residential",
            "bedrooms": 4,
            "bathrooms": 6,
            "lotSize": 6718,
            "lotUnit": "SqFt",
            "id": "9522998"
        }
    ]
}
```
### Request
```
http://<domain>/idx/feed/abor/json/?limit=500&sort=DESC&order=ListingPrice&criteria=%7B%22search_location%22:%22%22,%22minimum_price%22:%22%22,%22maximum_price%22:%22%22,%22search_type%22:%22%22,%22bounds%22:%7B%22ne%22:%7B%22lat%22:30.278790821848613,%22lng%22:-97.6583459104736%7D,%22sw%22:%7B%22lat%22:30.25818278186712,%22lng%22:-97.70452282087399%7D%7D,%22ne%22:%22%22,%22sw%22:%22%22,%22radius%22:%22%22,%22polygon%22:[%2230.271044781881002+-97.68040439741208,30.27037763023+-97.67611286298825,30.267004738592057+-97.67722866193844,30.266522887469957+-97.68173477308346,30.270637078633055+-97.68220684187008,30.271044781881002+-97.68040439741208%22]%7D
```
### Response (200)
```
{
  "limit": 3,
  "before": null,
  "after": null,
  "listingResults": [
    {
      "url": "http://<domain>/listing/9051182-5704-tura-ln-austin/",
      "photo": "https://4aa3ae4cc1ca24b64586-dafda21b792af8c00fe29aeb4465fc90.ssl.cf5.rackcdn.com/9051182-multi-family-11nhkll-l.jpg",
      "address": "5704 Tura Ln",
      "city": "Austin",
      "latitude": 30.267768,
      "longitude": -97.679779,
      "currency": "$",
      "listPrice": 340000,
      "propertyType": "Multi-Family",
      "bedrooms": 0,
      "bathrooms": 0,
      "lotSize": 1973,
      "lotSizeUnit": "SqFt",
      "id": "9051182"
    },
    {
      "url": "http://<domain>/listing/5025516-1110-christie-dr-austin/",
      "photo": "https://82f4fd3eb78a9570a4f2-1b4ea66f1af37438718b89a687a84cb9.ssl.cf5.rackcdn.com/5025516-residential-1fisxg2-l.jpg",
      "address": "1110 Christie Dr",
      "city": "Austin",
      "latitude": 30.268274,
      "longitude": -97.681463,
      "currency": "$",
      "listPrice": 300000,
      "propertyType": "Residential",
      "bedrooms": 3,
      "bathrooms": 2,
      "lotSize": 1625,
      "lotSizeUnit": "SqFt",
      "id": "5025516"
    },
    {
      "url": "http://<domain>/listing/3440943-5604-ledesma-rd-austin/",
      "photo": "https://278a10269c930e03a41b-5ae97641c1b1f4a7efec2cf6a4c00686.ssl.cf5.rackcdn.com/3440943-residential-1i4v2qh-l.jpg",
      "address": "5604 Ledesma Rd",
      "city": "Austin",
      "latitude": 30.269343,
      "longitude": -97.679921,
      "currency": "$",
      "listPrice": 290000,
      "propertyType": "Residential",
      "bedrooms": 3,
      "bathrooms": 1,
      "lotSize": 800,
      "lotSizeUnit": "SqFt",
      "id": "3440943"
    }
  ]
}
```


## `GET /idx/feed/<feed>/count/json/`
Takes in a set of criteria and returns the total count of all listings matching that criteria.

### Parameters:
- `criteria` (JSON) The criteria that the results should adhere to:
    - `location` (string) The state, county, city, subdivision, or area the listings should be in.
    - `min_price` (int) The minimum price the results should have.
    - `max_price` (int) The maximum price the results should have.
    - `property_type` (string | array) The type or types of listings the results should contain.
    - `has_pool` (bool) Whether the results should have pools (Y/N).
        - Valid inputs: `true`, `false`
    - `has_fireplace` (bool) Whether the results should have a fireplace.
        - Valid inputs: `true`, `false`
    - `is_waterfront` (bool) Whether the results should be waterfront property.
        - Valid inputs: `true`, `false`
    - `bounds` (JSON object) The corners of a map to bound the results by.
        - `ne` (JSON object) The NorthEast corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
        - `sw` (JSON object) The SouthWest corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
    - `radius` (JSON array) The coordinates and radius unit the desired listings should be in.
        - (JSON array element) There can be multiple radii in the radius array.
            - `lat` (float) Latitude of origin coordinate.
            - `lng` (float) Longitude of origin coordinate.
            - `radius` (float) Size of radius (in miles).
    - `polygon` (JSON array) The polygons that the desired listings should be in. There can be multiple polygons, 
          all of which should follow the [Well-Known Text Polygon Format](http://geoscript.org/examples/geom/wkt.html).

### Examples:
#### Request:
```
GET http://www.pickles.com:8000/idx/feed/abor/count/json/?&criteria={"property_type":"Residential"}
```

#### Response (200)
```
{
    "count": 627342
}
```


## `GET /idx/feed/<feed>/favorites/json/`
Returns a list  JSON-encoded list of favorite listings.

### Parameters:
- `user` (string) The GUID represented of the users whose favorites will be fetched

### Request
```
http://www.pickles.com:8000/idx/feed/abor/favorites/json/?user=E3392E7A-6F2A-11E8-A851-42010AF010AE
```
### Response (200)
```
{
    "favoriteListings": [
        {
            "id": "9",
            "user_id": "25",
            "agent_id": null,
            "associate": null,
            "mls_number": "9295744",
            "table": "_rewidx_listings",
            "idx": "abor",
            "type": "Residential",
            "city": "Lago Vista",
            "subdivision": "Brandon W. M.",
            "bedrooms": "6",
            "bathrooms": "6.00",
            "sqft": "5056",
            "price": "43000000",
            "user_note": "",
            "timestamp": "2018-06-14 12:06:49"
        },
        {
            "id": "11",
            "user_id": "25",
            "agent_id": null,
            "associate": null,
            "mls_number": "1562561",
            "table": "_rewidx_listings",
            "idx": "abor",
            "type": "Farms & Acreage",
            "city": "Lago Vista",
            "subdivision": "none",
            "bedrooms": "0",
            "bathrooms": "0.00",
            "sqft": "0",
            "price": "15750000",
            "user_note": "",
            "timestamp": "2018-06-14 12:06:39"
        }
    ]
}
```
### Response (400)
If an invalid user token is passed.

## `POST /idx/feed/<feed>/favorites/json/`
Create favorite listing for a user.

### Parameters:
- `listingId` (string) The MLS number of the listing to update
- `listingType` (string) The property type of the listing to update
- `user` (string) The GUID represented of the users whose favorites should be changed

### Request
```
POST http://www.pickles.com:8000/idx/feed/abor/favorites/json/
```

### Response (201)
```
{
    "id": "11",
    "user_id": "25",
    "agent_id": null,
    "associate": null,
    "mls_number": "1562561",
    "table": "_rewidx_listings",
    "idx": "abor",
    "type": "Farms & Acreage",
    "city": "Lago Vista",
    "subdivision": "none",
    "bedrooms": "0",
    "bathrooms": "0.00",
    "sqft": "0",
    "price": "15750000",
    "user_note": "",
    "timestamp": "2018-06-14 12:06:39"
}
```
### Response (400)
If an invalid user token is passed.

## `DELETE /idx/feed/<feed>/favorites/json/`
Remove favorite listing from a user.

### Parameters:
- `listingId` (string) The MLS number of the listing to update
- `listingType` (string) The property type of the listing to update
- `user` (string) The GUID represented of the users whose favorites should be changed

### Request
```
DELETE http://www.pickles.com:8000/idx/feed/abor/favorites/json/
```

### Response (204)
```
204 No Content
```
### Response (400)
If an invalid user token is passed.

## `POST /idx/feed/<feed>/save/json/`
Returns a new saved search.

### Parameters:
- `user` (string) The GUID represented of the users whose favorites will be fetched
- `criteria` (JSON) The criteria of the search to be saved
    - `bounds` (JSON object) The corners of a map to bound the results by.
        - `ne` (JSON object) The NorthEast corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
        - `sw` (JSON object) The SouthWest corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
    - `radius` (JSON array) The coordinates and radius unit the desired listings should be in.
        - (JSON array element) There can be multiple radii in the radius array.
            - `lat` (float) Latitude of origin coordinate.
            - `lng` (float) Longitude of origin coordinate.
            - `radius` (float) Size of radius (in miles).
    - `polygon` (JSON array) The polygons that the desired listings should be in. There can be multiple polygons, 
    all of which should follow the [Well-Known Text Polygon Format](http://geoscript.org/examples/geom/wkt.html).
- `title` The title of the string to be saved
- `frequency` (string) When the search should be saved
- `lead_id` (integer|null) The lead id that an agent is saving a search for.  Required if user is not provided.
- `user` (GUID|null) The user saving a given search.  Required if user is not provided.

### Request
```
http://www.pickles.com:8000/idx/feed/abor/save/json/
```

### Response (200)
```
{
    criteria:"a:6:{s:13:"minimum_price";s:6:"150000";s:13:"maximum_price";s:6:"425000";s:11:"search_type";a:1:{i:0;s:11:"Residential";}s:17:"minimum_bathrooms";s:1:"3";s:16:"minimum_bedrooms";s:1:"4";s:3:"map";a:5:{s:6:"bounds";b:1;s:2:"ne";s:20:"30.394723,-97.508571";s:2:"sw";s:20:"30.139417,-97.977550";s:6:"radius";N;s:7:"polygon";N;}}",
    frequency:"weekly",
    idx:"abor",
    title:"Test Search"
}    
```
### Response (400)
If an invalid user token is passed.

## `POST /idx/feed/<feed>/save/<id>/json/`
Returns an updated search.

### Parameters:
- `id` (integer) The search that is to be updated
- `user` (string) The GUID represented of the users whose favorites will be fetched
- `criteria` (JSON) The criteria of the search to be saved
    - `bounds` (JSON object) The corners of a map to bound the results by.
        - `ne` (JSON object) The NorthEast corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
        - `sw` (JSON object) The SouthWest corner to bound the map with.
            - `lat` (float) Latitude of coordinate.
            - `lng` (float) Longitude of coordinate.
    - `radius` (JSON array) The coordinates and radius unit the desired listings should be in.
        - (JSON array element) There can be multiple radii in the radius array.
            - `lat` (float) Latitude of origin coordinate.
            - `lng` (float) Longitude of origin coordinate.
            - `radius` (float) Size of radius (in miles).
    - `polygon` (JSON array) The polygons that the desired listings should be in. There can be multiple polygons, 
    all of which should follow the [Well-Known Text Polygon Format](http://geoscript.org/examples/geom/wkt.html).
- `title` The title of the string to be saved
- `frequency` (string) When the search should be saved
- `lead_id` (integer|null) The lead id that an agent is saving a search for.  Required if user is not provided.
- `user` (GUID|null) The user saving a given search.  Required if user is not provided.

### Request (200)
```
http://www.pickles.com:8000/idx/feed/abor/save/<id>/json/
```
### Response (400)
If an invalid user token is passed.


## `GET /drivetime/json/`
Returns a polygon representing the area which can be reached within the provided time from the provided location

### Parameters:
- `address` (string) The address that drivetime is being calculated to or from
- `lat` (float) The latitude of the above address
- `lng` (float) The longitude of the above address
- `direction` (string ['A'|'D'])  Arriving or departing form the location
- `arrivalTime` (string)  At what time to make the drivetime calculation
- `duration` (string)  The allowed drivetime

### Request
```
http://www.pickles.com:8000/drivetime/json/
```
### Response (200)
```
{
    polygon: "["34.1866850852966 -118.383436203003,34.1812133789063 -118.370325565338,34.1791749000549 -118.349254131317,34.1758918762207 -118.336658477783,34.17072057724 -118.319427967072,34.1616439819336 -118.317925930023,34.1553997993469 -118.291940689087,34.1535544395447 -118.278079032898,34.1419887542725 -118.269689083099,34.1286420822144 -118.25754404068,34.1149306297302 -118.241322040558,34.1110253334045 -118.247973918915,34.0929365158081 -118.206367492676,34.0793967247009 -118.224456310272,34.0550208091736 -118.211946487427,34.0482616424561 -118.23810338974,34.0306878089905 -118.258187770844,34.0347003936768 -118.284001350403,34.0218687057495 -118.300223350525,34.0108180046082 -118.318247795105,33.9995312690735 -118.330693244934,33.9889526367188 -118.340950012207,33.9761424064636 -118.356614112854,33.9407587051392 -118.368158340454,33.953161239624 -118.396139144897,33.953697681427 -118.39616060257,33.9660573005676 -118.42613697052,33.9656496047974 -118.441371917725,33.9890384674072 -118.462443351746,33.9888453483582 -118.473215103149,34.0008616447449 -118.483536243439,34.0213751792908 -118.508105278015,34.0290570259094 -118.521194458008,34.0387558937073 -118.557350635529,34.0400648117065 -118.560719490051,34.0854263305664 -118.474996089935,34.0955114364624 -118.476347923279,34.0989875793457 -118.478901386261,34.1160893440247 -118.482592105865,34.1273546218872 -118.480060100555,34.1353797912598 -118.487420082092,34.1505718231201 -118.474116325378,34.1651201248169 -118.48881483078,34.1704201698303 -118.468086719513,34.1702699661255 -118.448774814606,34.1739392280579 -118.431265354156,34.1866636276245 -118.418498039246,34.2179274559021 -118.409292697906,34.2012333869934 -118.401117324829,34.1866850852966 -118.383436203003"]",
    address: "1279 Ozeta Terrace, Los Angeles, CA, USA",
    direction: "A",
    duration: "30",
    arrivalTime: "08:15"
}
```
### Response (400)
If an invalid user token is passed.
