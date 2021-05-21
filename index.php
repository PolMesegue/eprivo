<html>

<head>
    <title>your_domain website</title>

    <style>
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr 1fr 1fr;
            gap: 0px 0px;
            grid-template-areas:
                "search search search search"
                "list list tracking tracking"
                "list list tracking tracking"
                "list list web web"
                "list list web web";
        }

        .search {
            grid-area: search;
        }

        .tracking {
            grid-area: tracking;
        }

        .web {
            grid-area: web;
        }

        .list {
            grid-area: list;
        }
    </style>
</head>

<body>
    <div class="grid-container">
        <div class="search"></div>
        <div class="tracking"></div>
        <div class="web"></div>
        <div class="list"></div>
    </div>
</body>

</html>