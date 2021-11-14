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
                            <tbody>
                                <tr v-for="line in pagedLogLines" :key=line>
                                    <td class="column-timestamp"><pre>{{line.timestamp}}</pre></td>
                                    <td class="column-level"><pre>{{line.type}}</pre></td>
                                    <td><pre>{{line.content}}</pre></td>
                                </tr>
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

            <section v-if='(this.total == 0) && (logfiles.length != 0)' class="k-system-view-section">
                <div class="k-system-info-box">
                    <k-empty icon="book">Logfile is empty</k-empty>
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
        'limit'
    ],
    data() {
        return {
            page: 1,
            logLines: []
        };
    },
    methods: {
        fetch: function() {
            fetch('/kirbylogbook/' + this.selectedLogfile)
            .then(response => response.json())
            .then(data => {
                // Reset paging
                this.page = 1;
                // Parse loglines
                if (Array.isArray(data)) {
                    // KirbyLog format
                    this.logLines = data;
                } else {
                    // Can be anything
                    this.logLines = Object.entries(data).map(item => item[1]);
                }
            });
        },
        paginate: function({page, limit}) {
            this.page = page;
            this.limit = limit;
        }
    },
    computed: {
        logfilesOptions: function() {
            return this.logfiles.map(logfilename => ({value: logfilename, text: logfilename}));
        },
        isKirbyLogPluginLog: function() {
            if (this.logLines[0] == undefined) return false;

            var kirbyLogSchema = [ "timestamp", "type", "content" ];
            return (this.hasKirbyLogPlugin) &&
                   (JSON.stringify(Object.keys(this.logLines[0])) == JSON.stringify(kirbyLogSchema));
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
                tr:nth-of-type(even) {
                    background-color: var(--color-gray-100);
                }
            }

            .column-level, .column-timestamp {
                width: 1%;
                padding-right: 1em;
                white-space: nowrap;
            }
        }

        ol {
            li {
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
    }

    &-column {
        &-select {
            align-self: center;
        }
        &-pagination {
            div .k-pagination {
                text-align: center;

                @media (screen and min-width: 65em) {
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
