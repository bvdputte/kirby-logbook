<template>
    <k-inside>
        <k-view class="k-logbook-view">

            <k-header>
                {{title}}
            </k-header>

            <section v-if='logfiles.length >0' class="k-logbook-actions">
                <k-grid gutter="medium">
                    <k-column width="1/3" class="k-logbook-column-select">
                        <k-select-field
                            v-model="selectedLogfile"
                            :options="logfilesOptions"
                            @input="fetch()"
                            label="Logfile"
                            name="k-logbook__log-select"
                            type="select"
                            icon="angle-down" />
                    </k-column>
                    <k-column width="1/3" v-if='this.logLevels.length > 1'>
                        <k-select-field
                            v-model="filterLevel"
                            :options="logLevels"
                            @input="filter()"
                            label="Level"
                            type="select"
                            icon="angle-down" />
                    </k-column>
                    <k-column width="1/3">
                        <k-text-field v-model="filterText"
                            type="text"
                            @change="filter()"
                            label="Search"
                            placeholder="Search"
                            icon="search"/>
                    </k-column>
                    <k-column v-if='logData.length == maxLogLines'>
                        <k-info-field v-bind:text="maxLogLinesReachedMessage" />
                    </k-column>
                </k-grid>
            </section>

            <section v-if='this.total > 0' class="k-system-view-section">
                <div class="k-system-info-box k-logbook-pane">
                    <template v-if='isKirbyLogPluginLog'>
                        <table>
                            <thead>
                                <tr>
                                    <th class="column-timestamp">Timestamp</th>
                                    <th class="column-level">Level</th>
                                    <th>Entry</th>
                                </tr>
                            </thead>
                            <tbody v-for="line in pagedLogLines" :key=line>
                                <tr>
                                    <td class="column-timestamp"><pre>{{line.timestamp}}</pre></td>
                                    <td class="column-level"><pre>{{line.type}}</pre></td>
                                    <template v-if='line.extra != ""'>
                                        <td class="column-entry column-entry__with-extras">
                                            <span class="extras-trigger" @click="showExtras($event)">
                                                <k-icon type="add" />
                                            </span>
                                            <pre>{{line.content}}</pre>
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td class="column-entry">
                                            <pre>{{line.content}}</pre>
                                        </td>
                                    </template>
                                </tr>
                                <template v-if='line.extra != ""'>
                                    <tr class="line-with-extras--hidden">
                                        <td colspan="3">
                                            <ol class='line__extras'>
                                                <li v-for="(extra, index) in line.extra" :key="index">{{extra}}</li>
                                            </ol>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                    <template v-else>
                        <ol>
                            <li v-for="(line, index) in pagedLogLines" :key="index">
                                <pre>{{line}}</pre>
                            </li>
                        </ol>
                    </template>
                </div>
                <div class="k-logbook-pane-caption">
                    <k-grid gutter="medium">
                        <k-column width="2/3" class="k-logbook-column-pagination-summary">
                            Showing {{pageStart+1}} to {{pageEnd}} from {{total}} log entries
                        </k-column>
                        <k-column width="1/3" class="k-logbook-column-pagination">
                            <k-pagination
                                :details="false"
                                :page="page"
                                :limit="limit"
                                :total="total"
                                @paginate="paginate" />
                        </k-column>
                    </k-grid>
                </div>

            </section>

            <section v-if='(this.logData.length == 0) && (logfiles.length != 0)' class="k-system-view-section">
                <div class="k-system-info-box">
                    <k-empty icon="book">Logfile is empty</k-empty>
                </div>
            </section>

            <section v-if='(this.logData.length > 0) && (this.total == 0) && (logfiles.length != 0)' class="k-system-view-section">
                <div class="k-system-info-box">
                    <k-empty icon="book">No matches found</k-empty>
                </div>
            </section>

            <section v-if='logfiles.length == 0' class="k-system-view-section">
                <div class="k-system-info-box">
                    <k-empty icon="book">No logfiles found</k-empty>
                </div>
            </section>

        </k-view>
    </k-inside>
</template>

<script>
export default {
    name: 'LogBookArea',
    props: [
        'title',
        'logfiles',
        'selectedLogfile',
        'hasKirbyLogPlugin',
        'maxLogLines',
        'limit'
    ],
    data() {
        return {
            page: 1,
            logData: [],
            logLines: [],
            logLevels: [],
            filterLevel: '',
            filterText: ''
        };
    },
    methods: {
        fetch: function() {
            fetch(this.$urls.site + '/kirbylogbook/' + this.selectedLogfile)
            .then(response => response.json())
            .then(data => {
                this.initializeComponent(data);
            });
        },
        initializeComponent: function(data) {
                // Reset
                this.page = 1;
                this.logLevels = [];
                this.filterLevel = '';
                this.filterText = '';
                // Parse loglines
                if (Array.isArray(data)) {
                    // KirbyLog format
                    this.logLines = this.logData = data;

                    // Set loglevels filter
                    // 1. Get all types
                    var filters = this.logLines.map(line => line.type);
                    // 2. Make unique
                    var uniqueFilters = [...new Set(filters)];
                    this.logLevels = uniqueFilters.map(level => ({value: level, text: level}));
                } else {
                    // Unrecognized log format.
                    this.logLines = this.logData = Object.entries(data).map(item => item[1]);
                }
        },
        filter: function() {
            var data = this.logData;
            // Filter via level
            if (this.filterLevel != '') {
                data = data.filter( line => line.type == this.filterLevel );
            }
            // Filter via text search
            if (this.filterText != '') {
                if (this.isKirbyLogPluginLog) {
                    data = data.filter( line => line.content.includes(this.filterText) );
                } else {
                    data = data.filter( line => line.includes(this.filterText) );
                }
            }
            this.logLines = data;
        },
        paginate: function({page, limit}) {
            this.page = page;
            this.limit = limit;
        },
        showExtras: function(event){
            let row = event.target.closest("tr");
            row.closest("table").rows[ row.rowIndex + 1 ].classList.toggle('line-with-extras--hidden')
        }
    },
    computed: {
        logfilesOptions: function() {
            return this.logfiles.map(logfilename => ({value: logfilename, text: logfilename}));
        },
        isKirbyLogPluginLog: function() {
            if (this.logData.length == 0) return false;

            var kirbyLogSchema = [ "timestamp", "type", "content", "extra" ];
            return (this.hasKirbyLogPlugin) &&
                   (JSON.stringify(Object.keys(this.logData[0])) == JSON.stringify(kirbyLogSchema));
        },
        pagedLogLines: function() {
            return this.logLines.slice(this.pageStart, this.pageEnd);
        },
        total: function() {
            return this.logLines.length;
        },
        pageStart: function() {
            return (this.page-1) * this.limit;
        },
        pageEnd: function() {
            var projectedSet = ((this.page-1) * this.limit) + this.limit;
            if (projectedSet >= this.total) return this.total;
            return projectedSet;
        },
        maxLogLinesReachedMessage: function() {
            return '⚠️ Only last ' + this.maxLogLines + ' log lines fetched.<br>Increase threshold to fetch more, or tail log on the server.'
        }
    },
    created() {
        this.fetch();
    }
}
</script>

<style lang="scss">
.k-logbook {
    &-actions {
        margin-bottom: 1.5em;
    }
    &-pane {
        background-color: var(--color-white);
        overflow-x: scroll;
        box-shadow: var(--shadow);

        table {
            border-collapse: collapse;
            text-align: left;

            th {
                border-bottom: 1px solid var(--color-border);
            }
            th, td {
                padding: .5rem;
            }

            tbody {
                td {
                    padding-top: .25rem;
                    padding-bottom: .25rem;
                }

                tr:first-child td {
                    padding-top: .5rem;
                }

                tr:last-child td {
                    padding-bottom: .5rem;
                }
                &:nth-of-type(even) {
                    background-color: var(--color-gray-100);
                }
            }

            .column {
                &-level, &-timestamp {
                    width: 1%;
                    padding-right: 1em;
                    white-space: nowrap;
                }

                &-entry {
                    &__with-extras {
                        display: flex;
                        gap: .5em;

                        .extras-trigger {
                            cursor: pointer;
                        }
                    }
                }
            }
        }

        ol {
            & > li {
                padding-top: .25rem;
                padding-bottom: .25rem;

                &:first-child {
                    padding-top: .5rem;
                }

                &:last-child {
                    padding-bottom: .5rem;
                }

                &:nth-of-type(even) {
                    background-color: var(--color-gray-100);
                }
            }
        }

        &-caption {
            margin-top: 1em;
        }

        .line {
            &-with-extras--hidden {
                display: none;
            }
            &__with-extras {
                position: relative;
                cursor: pointer;

                &:after {
                    content: "";
                    position: absolute;
                    z-index: 1;
                    bottom: 0;
                    left: .3em;
                    pointer-events: none;
                    background-image: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255, 1) 88%);
                    width: calc(100% + 1px);
                }
            }

            &__extras {
                overflow: hidden;
                margin-left: .3em;
                border-left: 1px solid var(--color-gray-200);
                padding-left: .5em;
                color: var(--color-gray-600);

                & > li {
                    padding: 0;
                    background-color: transparent !important;
                }
            }
        }
    }

    &-column {
        &-select {
            align-self: center;
        }
        &-pagination {
            div .k-pagination {
                text-align: center;

                @media (screen) and (min-width: 65em) {
                    text-align: right;
                }
            }
        }
        &-pagination-summary {
            align-self: center;
            font-size: .8em;
        }
    }
}

.k-field-name-k-logbook__log-select {
    option[value=""] {
        display: none;
    }
}
</style>
