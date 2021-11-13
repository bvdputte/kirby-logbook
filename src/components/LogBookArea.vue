<template>
    <k-inside>
        <k-view class="k-logbook-view">

            <k-header>
                {{title}}
            </k-header>

            <section v-if='logfiles.length >0'>
                <header class="k-system-view-section-header">
                <k-headline>
                    <select v-model="selectedLogfile" @change="fetchLog($event)">
                    <option v-for="logfile in logfiles" :key="logfile">
                        {{logfile}}
                    </option>
                    </select>
                </k-headline>
                </header>
            </section>

            <section v-if='this.logLinesCount > 0' class="k-system-view-section">
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
                        <tr v-for="line in logLinesSet" :key=line>
                            <td class="column-timestamp">{{line.timestamp}}</td>
                            <td class="column-level">{{line.type}}</td>
                            <td>{{line.content}}</td>
                        </tr>
                        </tbody>
                    </table>
                    </template>
                    <template v-else>
                    <ol>
                        <li v-for="line in logLinesSet" :key=line>
                            {{line[1]}}
                        </li>
                    </ol>
                    </template>
                </div>

                <div class="k-logbook-pagination">
                    <div>
                        Showing {{paginationPage+1}}&nbsp;to&nbsp;{{paginationPageMax}} from {{logLinesCount}} log entries
                        <div v-if='!hasNextPaginationSet && (logLinesCount >= maxLogLines)'>
                            ⚠️ Maximum log entries displayed. Increase 'maxLogLines' in LogBook plugin settings, or check log on server for more entries.
                        </div>
                    </div>
                    <nav class="k-button-group k-prev-next">
                        <span class="k-button" :data-disabled='!hasPreviousPaginationSet'>
                            <span v-if='!hasPreviousPaginationSet' class="k-button-icon k-icon k-icon-angle-left">
                                <svg viewBox="0 0 16 16"><use xlink:href="#icon-angle-left"></use></svg>
                            </span>
                            <button v-else class="k-button-icon k-icon k-icon-angle-left" @click="paginationPrevious($event)" aria-label="Previous">
                                <svg viewBox="0 0 16 16"><use xlink:href="#icon-angle-left"></use></svg>
                            </button>
                        </span>
                        <span class="k-button" :data-disabled='!hasNextPaginationSet'>
                            <span v-if='!hasNextPaginationSet' class="k-button-icon k-icon k-icon-angle-right">
                                <svg viewBox="0 0 16 16"><use xlink:href="#icon-angle-right"></use></svg>
                            </span>
                            <button v-else class="k-button-icon k-icon k-icon-angle-right" @click="paginationNext($event)" aria-label="Next">
                                <svg viewBox="0 0 16 16"><use xlink:href="#icon-angle-right"></use></svg>
                            </button>
                        </span>
                    </nav>
                </div>
            </section>

            <section v-if='(this.logLinesCount == 0) && (logfiles.length != 0)' class="k-system-view-section">
                <div class="k-system-info-box k-logbook-pane">
                    <p>Empty logfile</p>
                </div>
            </section>

            <section v-if='logfiles.length == 0' class="k-system-view-section">
                <div class="k-system-info-box k-logbook-pane">
                    <p>No logfiles found</p>
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
        'logData',
        'maxLogLines',
        'hasKirbyLogPlugin',
        'paginationPage',
        'paginationSize'
    ],
    methods: {
        fetchLog: function(event) {
            fetch('/kirbylogbook/' + event.target.value)
            .then(response => response.json())
            .then(data => {
                this.paginationPage = 0;
                this.logData = data;
            });
        },
        parseLogLines: function() {
            var lines;
            if (Array.isArray(this.logData)) {
                lines = this.logData;
            } else {
                lines = Object.entries(this.logData);
            }
            return lines;
        },
        // Pagination
        paginationNext: function(event) {
            this.paginationPage += this.paginationSize;
        },
        paginationPrevious: function(event) {
            this.paginationPage -= this.paginationSize;
        }
    },
    computed: {
        logLines: function() {
            return this.parseLogLines();
        },
        isKirbyLogPluginLog: function() {
            if (this.logData[0] == undefined) return false;

            var kirbyLogSchema = [ "timestamp", "type", "content" ];
            return (this.hasKirbyLogPlugin) &&
                   (JSON.stringify(Object.keys(this.logData[0])) == JSON.stringify(kirbyLogSchema));
        },
        // Pagination
        logLinesCount: function() {
            return this.logLines.length
        },
        logLinesSet: function() {
            return this.logLines.slice(this.paginationPage, this.paginationPage+this.paginationSize);
        },
        paginationPageMax: function() {
            if (this.hasNextPaginationSet) return this.paginationPage+this.paginationSize;

            return this.logLinesCount;
        },
        hasPagination: function() {
            if (this.logLinesCount > this.paginationSize) return true;
        },
        hasNextPaginationSet: function() {
            return ((this.paginationPage+this.paginationSize) < this.logLinesCount);
        },
        hasPreviousPaginationSet: function() {
            return ((this.paginationPage-this.paginationSize) >= 0);
        }
    },
    created() {
        this.paginationPage = 0;
    }
}
</script>

<style lang="scss" scoped>
.k-logbook {
    &-pane {
        background-color: #fff;
        margin-top: 1.5em;
        overflow-x: scroll;

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
                    background-color: #f5f5f5;
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
                    background-color: #f5f5f5;
                }
            }
        }

        & > p {
            padding: .5rem;
        }
    }
    &-pagination {
        font-size: .8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
}
</style>
