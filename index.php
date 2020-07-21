<!DOCTYPE html>
<html>

<head>
    <title>Tester</title>
    <meta charset="UTF-8">
    <meta content='api,tester,test,接口,测试,调试,http,https,开发,markdown,文档,生成' name='Keywords'>
    <meta content='一个轻量API接口调试工具，支持自定义Header，自定义Cookies，支持GET/POST/PUT/DELETE/PATCH/TRACE/OPTIONS等请求方式，支持快速生成Markdown接口文档，支持分享当前请求链接等' itemprop='description' name='Description'>
    <link rel="stylesheet" href="/static/css/element.css">
    <style>
        body,
        html {
            padding: 0;
            margin: 0;
            background-color: #f5f5f5;
        }

        * {
            font-family: consolas, PingFang SC, Microsoft YaHei;
        }

        [v-cloak] {
            visibility: hidden !important;
        }

        .el-tabs--border-card {
            box-shadow: none;
        }

        #app {
            margin: 20px;
        }

        .logo {
            font-size: 32px;
            color: #666;
        }

        .no-select * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .el-select .el-input {
            width: 100px;
        }

        .method {
            margin-left: 20px !important;
            border-left: 1px solid #ddd;
        }

        .type {
            cursor: pointer;
        }

        .tips {
            font-size: 12px;
            color: #999;
            padding-left: 80px;
        }

        .contentType {
            margin-right: 10px !important;
        }

        .contentType .el-input {
            width: 90px;
        }

        .el-tabs__header,
        .el-tabs__nav-scroll,
        .el-tabs--border-card {
            border-radius: 5px;
        }

        textarea {
            resize: none !important;
        }

        pre {
            overflow: auto;
        }

        pre>* {
            font-size: 14px;
            margin: 0;
        }

        .header {
            margin-bottom: 10px;
            height: 70px !important;
        }

        .key {
            color: #333;
        }

        .hljs-string {
            color: green !important;
        }

        .hljs-number {
            color: orangered !important;
        }

        .hljs-literal {
            color: red !important;
        }

        .hljs {
            background-color: white !important;
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background-color: rgba(50, 50, 50, 0.1);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.2);
        }

        ::-webkit-scrollbar-button {
            background-color: transparent;
        }

        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        .input-with-select {
            display: block;
        }
    </style>
</head>

<body>
    <div id="app" v-cloak>
        <el-container>
            <el-header class="header">
                <a href="/">
                    <img src="/static/images/logo.png" height="80px" />
                </a>
                <span style="float:right;margin-bottom:10px;" class="no-select">
                    <el-link type="primary" href="https://gitee.com/hamm/tester/attach_files" target="_blank">下载客户端</el-link>
                    <el-link type="primary" href="javascript:;" @click.native="dialogForSetting=true">环境变量</el-link>
                    <el-link type="primary" href="https://gitee.com/hamm/tester/blob/master/README.md" target="_blank">配置教程</el-link>
                    <el-link type="primary" href="https://gitee.com/hamm/tester" target="_blank">开源地址</el-link>
                </span>
            </el-header>
            <el-main>
                <div>
                    <el-autocomplete placeholder="请输入请求的URL" @input="requestUrlChanged" class="input-with-select no-select" v-model="request.url" :fetch-suggestions="querySearch" @select="handleSelect">
                        <el-link slot="prepend" class="type no-select" title="切换线上和本地版本" @click.native="changeType">{{nowType}}</el-link>
                        <el-select class="method no-select" v-model="request.method" slot="prepend" placeholder="请选择请求方式">
                            <el-option :label="item" :value="item" v-for="item in factory.methodList"></el-option>
                        </el-select>
                        <el-select slot="append" class="contentType no-select" v-model="factory.contentType" placeholder="ContentType" @change="contentTypeChanged">
                            <el-option :key="item.value" :label="item.label" :value="item.value" v-for="item in factory.contentTypeList"> <span style="float: left">{{ item.label }}　　</span>
                                <span style="float: right; color: #8492a6; font-size: 13px">{{ item.value }}</span>

                            </el-option>
                        </el-select>
                        <el-button slot="append" icon="el-icon-s-promotion" @click="onSubmit" v-loading.fullscreen.lock="loading">请求</el-button>
                    </el-autocomplete>
                </div>
                <br>
                <el-tabs type="border-card" v-model="factory.requestActive">
                    <el-tab-pane label="Body" name="Body">
                        <el-input type="textarea" rows="8" class="data" placeholder="a=b&c=d&#10;&#10;{'a':'b','c':'d'}&#10;&#10;xml" v-model="request.body"></el-input>
                    </el-tab-pane>
                    <el-tab-pane label="Header" name="Header">
                        <el-input type="textarea" rows="8" class="data" placeholder="content-type: application/json;&#10;User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36" v-model="request.header"></el-input>
                    </el-tab-pane>
                    <el-tab-pane label="Cookie" name="Cookie">
                        <el-input type="textarea" rows="8" class="data" v-model="request.cookie" placeholder="access_token=abcdefghijklmnopqrstuvwxyz;&#10;可直接复制Chrome控制台Set-Cookie的内容"></el-input>
                    </el-tab-pane>
                </el-tabs>
                <br>
                <el-tabs type="border-card">
                    <el-tab-pane label="Body">
                        <pre v-html="response.body"></pre>

                    </el-tab-pane>
                    <el-tab-pane label="header">
                        <pre v-html="response.header"></pre>

                    </el-tab-pane>
                    <el-tab-pane label="Detail">
                        <pre v-html="response.detail"></pre>

                    </el-tab-pane>
                    <el-tab-pane label="MarkDown">
                        <el-link type="primary" href="https://md.hamm.cn" target="_blank" style="float:right;margin-bottom:10px;">MarkDown编辑器</el-link>
                        <el-input type="textarea" autosize placeholder="文档读取中" v-model="response.markdown"></el-input>
                    </el-tab-pane>
                </el-tabs>
            </el-main>
        </el-container>
        <!-- 环境设置框 -->
        <el-dialog title="环境设置" :visible.sync="dialogForSetting" :modal-append-to-body='false'>
            <el-form status-icon>
                <el-form-item label="在线地址" label-width="80px">
                    <el-input size="medium" autocomplete="off" v-model="urlList.online" placeholder="在线域名 如https://tester.hamm.cn/"></el-input>
                </el-form-item>
                <el-form-item label="本地地址" label-width="80px">
                    <el-input size="medium" autocomplete="off" v-model="urlList.local" placeholder="本地域名 如http://127.0.0.1/"></el-input>
                </el-form-item>
            </el-form>
            <div class="tips">生成测试用例时会将本地地址替换为线上地址<br>切换调试环境时将会为你自动切换请求地址</div>
            <div slot="footer" class="dialog-footer">
                <el-button type="primary" @click="saveUrlList">配置完成</el-button>
            </div>
        </el-dialog>
    </div>
</body>
<script src="/static/js/vue-2.6.10.min.js"></script>
<script src="/static/js/axios.min.js"></script>
<script src="/static/js/element.js"></script>
<link rel="stylesheet" href="/static/css/highlight.min.css">
<script src="/static/js/highlight.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                maxResponseLength: 10240,
                loading: false,
                urlList: {
                    online: "",
                    local: ""
                },
                nowType: "线上版",
                dialogForSetting: false,
                request: {
                    method: "POST",
                    url: "https://tester.hamm.cn/test.php",
                    body: "access_token=abcdefghijklmnopqrstuvwxyz",
                    header: "",
                    cookie: "",
                    value: "",
                },
                response: {
                    body: "",
                    detail: "",
                    header: "",
                    markdown: ""
                },
                historyMax: 100, //最大历史
                historyList: [], //历史
                factory: {
                    requestActive: "Body",
                    header: {
                        'Content-Type': 'application/x-www-form-urlencoded;',
                    },
                    contentType: 'application/x-www-form-urlencoded;',
                    contentTypeList: [{
                            label: 'JSON',
                            value: 'application/json;'
                        },
                        {
                            label: 'XML',
                            value: 'application/xml;'
                        },
                        {
                            label: '表单',
                            value: 'application/x-www-form-urlencoded;'
                        },
                        {
                            label: '文本',
                            value: 'text/plain;'
                        },
                        {
                            label: 'HTML',
                            value: 'text/html;'
                        }
                    ],
                    methodList: [
                        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'PATCH'
                    ]
                }
            }
        },
        created() {
            axios.defaults.timeout = 10000;
            this.updateData();
            var key = this.get_url_params();
            if (key) {
                this.getData(key);
            }
            try {
                this.urlList = !localStorage.getItem('urlList') ? {
                    online: "",
                    local: ""
                } : JSON.parse(localStorage.getItem('urlList'));
            } catch (e) {
                this.urlList = {
                    online: '',
                    local: ''
                };
            }
            try {
                this.historyList = !localStorage.getItem('history') ? [] : JSON.parse(localStorage.getItem('history'));
            } catch (e) {
                this.historyList = [];
            }
        },
        updated() {
            document.querySelectorAll('pre').forEach(function(block) {
                hljs.highlightBlock(block);
            });
        },
        methods: {
            handleSelect(item) {
                this.request = item;
                console.log(item.url)
                console.log(item);
            },
            querySearch(queryString, cb) {
                //设置历史
                cb(JSON.parse(JSON.stringify(this.historyList)));
            },
            requestUrlChanged() {
                this.request.url = this.request.url.replace(/\s+/g, "");
            },
            saveUrlList() {
                this.dialogForSetting = false;
                this.urlList.online = this.urlList.online.replace(/\s+/g, "");
                this.urlList.local = this.urlList.local.replace(/\s+/g, "");
                localStorage.setItem('urlList', JSON.stringify(this.urlList));
                this.$message({
                    message: '你的环境变量配置成功！',
                    type: 'success'
                });
            },
            //切换调试环境
            changeType() {
                if (this.nowType == "线上版") {
                    this.nowType = "本地版";
                    this.request.method = 'POST';
                    this.factory.methodList = ['GET', 'POST'];
                    this.request.url = this.request.url.replace(this.urlList.online, this.urlList.local);
                } else {
                    this.nowType = "线上版";
                    this.factory.methodList = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'PATCH'];
                    this.request.url = this.request.url.replace(this.urlList.local, this.urlList.online);
                }
            },
            contentTypeChanged() {
                this.factory.requestActive = 'Header';
                this.updateData();
            },
            updateData() {
                this.factory.header['Content-Type'] = this.factory.contentType;
                var headerStr = '';
                for (var key in this.factory.header) {
                    headerStr += key + ":" + this.factory.header[key] + "\n";
                }
                this.request.header = headerStr;
            },
            getData(key) {
                var that = this;
                axios.get('api.php?key=' + key)
                    .then(function(response) {
                        if (response.data.code == 200) {
                            that.$message({
                                message: '数据读取成功',
                                type: 'success'
                            });
                            try {
                                var obj = JSON.parse(response.data.data);
                                that.request.url = obj.url;
                                that.request.method = obj.method;
                                that.request.body = obj.body;
                                that.request.cookie = obj.cookie;
                                that.factory.header = {};
                                for (let index in obj.header) {
                                    var match = obj.header[index].match(/(.*?):/);
                                    if (match) {
                                        var value = obj.header[index].replace(match[0], '');
                                        that.factory.header[match[1]] = value;
                                        if (match[1].toLowerCase() == 'content-type') {
                                            that.factory.contentType = value;
                                        }
                                    }
                                }
                                that.updateData();
                            } catch (error) {
                                that.response.body = that.html2Escape(response.data.data.body);
                            }
                        } else {
                            that.$message.error(response.data.msg);
                        }
                    })
                    .
                catch(function(error) {
                    that.$message.error('获取服务器数据异常');
                });
            },
            onSubmit() {
                var that = this;
                that.loading = true;
                if (that.request.url.indexOf('http://') == -1 && that.request.url.indexOf('https://') == -1) {
                    that.request.url = "http://" + that.request.url;
                }
                var historyItem = JSON.parse(JSON.stringify(that.request));
                historyItem.value = historyItem.url;
                if (that.historyList.length > that.historyMax) {
                    that.historyList.pop();
                }
                that.historyList.unshift(historyItem);
                localStorage.setItem('history', JSON.stringify(that.historyList));
                if (that.nowType == "线上版") {
                    var arr = that.request.header.split('\n');
                    that.factory.header = {};
                    for (var index in arr) {
                        var match = arr[index].match(/(.*?):/);
                        if (match) {
                            var value = arr[index].replace(match[0], '');
                            that.factory.header[match[1]] = value;
                            if (match[1].toLowerCase() == 'content-type') {
                                that.factory.contentType = value;
                            }
                        }
                    }
                    that.updateData();
                    axios.post('api.php', that.request)
                        .then(function(response) {
                            that.loading = false;
                            if (response.data.code == 200) {
                                that.$message({
                                    message: '请求成功',
                                    type: 'success'
                                });
                                that.decodeResponseDataOnline(response);
                            } else {
                                that.$message.error(response.data.msg);
                            }
                        })
                        .
                    catch(function(error) {
                        that.loading = false;
                        console.log(error.request);
                        console.log(error.message);
                        if (error.message == 'timeout of 10000ms exceeded') {
                            that.$message.error("请求API接口网络超时！");
                        } else {
                            that.$message.error('出现异常，你可以控制台查看错误');
                        }
                    });
                } else {
                    //走本地
                    var request = JSON.parse(JSON.stringify(that.request));
                    var historyItem = request;
                    request.url = request.url.replace(that.urlList.local, that.urlList.online);
                    axios.post('api.php?local=1', request)
                        .then(function(response) {
                            if (response.data.code == 200) {
                                var key = response.data.data;
                                historyItem.key = key;
                                if (that.historyList.length > that.historyMax) {
                                    that.historyList.pop();
                                }
                                that.historyList.unshift(historyItem);
                                localStorage.setItem('history', JSON.stringify(that.historyList));
                                switch (that.request.method) {
                                    case 'POST':
                                        axios.post(that.request.url, that.request.body, {
                                                headers: that.request.headers
                                            })
                                            .then(function(response) {
                                                that.loading = false;
                                                if (!response.headers) {
                                                    that.$message.error("请求超时，请稍候重试！");
                                                    return;
                                                }
                                                that.$message({
                                                    message: '请求成功',
                                                    type: 'success'
                                                });
                                                var resp = {
                                                    header: response.headers,
                                                    body: response.data,
                                                    http_code: 200,
                                                    detail: response,
                                                    key: key
                                                };
                                                that.decodeResponseDataLocal(resp);
                                            })
                                            .
                                        catch(function(error) {
                                            that.loading = false;
                                            if (error.response) {
                                                var resp = {
                                                    header: error.response.headers,
                                                    body: error.response.data,
                                                    http_code: error.response.status,
                                                    detail: error.response,
                                                    key: key
                                                };
                                                that.decodeResponseDataLocal(resp);
                                            } else if (error.request) {
                                                console.log(error.request);
                                                console.log(error.message);
                                                if (error.message == 'timeout of 10000ms exceeded') {
                                                    that.$message.error("请求API接口网络超时！");
                                                } else {
                                                    that.$message.error("本地测试请先配置允许跨域和安全访问！");
                                                }
                                            } else {
                                                that.$message.error(error.message);
                                            }
                                        });
                                        break;
                                    case 'GET':
                                        axios.get(that.request.url, {
                                                headers: that.request.headers
                                            })
                                            .then(function(response) {
                                                that.loading = false;
                                                if (!response.headers) {
                                                    that.$message.error("请求超时，请稍候重试！");
                                                    return;
                                                }
                                                that.$message({
                                                    message: '请求成功',
                                                    type: 'success'
                                                });
                                                var resp = {
                                                    header: response.headers,
                                                    body: response.data,
                                                    http_code: 200,
                                                    detail: response,
                                                    key: key
                                                };
                                                that.decodeResponseDataLocal(resp);
                                            })
                                            .
                                        catch(function(error) {
                                            that.loading = false;
                                            if (error.response) {
                                                var resp = {
                                                    header: error.response.headers,
                                                    body: error.response.data,
                                                    http_code: error.response.status,
                                                    detail: error.response,
                                                    key: key
                                                };
                                                that.decodeResponseDataLocal(resp);
                                            } else if (error.request) {
                                                console.log(error.request);
                                                console.log(error.message);
                                                if (error.message == 'timeout of 10000ms exceeded') {
                                                    that.$message.error("请求API接口网络超时！");
                                                } else {
                                                    that.$message.error("本地测试请先配置允许跨域和安全访问！");
                                                }
                                            } else {
                                                that.$message.error(error.message);
                                            }
                                        });
                                        break;
                                    default:
                                }
                            } else {
                                that.$message.error(response.data.msg);
                            }
                        })
                        .
                    catch(function(error) {
                        console.log(error)
                        that.$message.error('出现异常，你可以控制台查看错误');
                    });
                }
            },
            //解析本地版本返回的数据
            decodeResponseDataLocal(response) {
                var that = this;
                if (response.body.length > that.maxResponseLength) {
                    that.response.body = '返回文本超长，为了Tester的性能考虑，这里不予显示。';
                } else {
                    try {
                        if (typeof(response.body) == "object") {
                            that.response.body = unescape(that.JsonFormat(response.body));
                        } else {
                            that.response.body = that.JsonFormat(JSON.parse(response.body));
                        }
                    } catch (error) {
                        that.response.body = that.html2Escape(response.body);
                    }
                }
                delete response.detail.data;
                try {
                    that.response.detail = unescape(that.JsonFormat(response.detail))
                } catch (error) {
                    that.response.detail = that.html2Escape(response.detail);
                }
                try {
                    that.response.header = unescape(that.JsonFormat(response.header))
                } catch (error) {
                    that.response.header = that.html2Escape(response.header);
                }
                that.response.httpcode = response.http_code;

                location.href = "/#/" + response.key;

                that.response.markdown = '';
                that.response.markdown += '## xxx API接口文档\n\n';
                that.response.markdown += '> 本文档由 [Tester](https://tester.hamm.cn) 自动生成，最后修改时间 ' + that.getNowDateTime() +
                    '\n\n';

                that.response.markdown += '#### 一、接口说明\n\n';
                that.response.markdown += '你可以在这里对接口进行一些简单的描述\n\n';
                that.response.markdown += '#### 二、请求方式\n\n';
                that.response.markdown += '```' + that.request.method + ' ' + (that.request.url.replace(that.urlList.local, that.urlList.online)) + '```\n\n';
                that.response.markdown += '#### 三、请求参数\n\n';
                if (that.request.method != 'GET') {
                    switch (that.factory.contentType) {
                        case 'application/json;':
                            try {
                                var obj = JSON.parse(that.request.body);
                                that.response.markdown += '|字段|类型|必填|说明|\n';
                                that.response.markdown += '|-|-|-|-|\n';
                                that.response.markdown += that.getJsonMarkdown(obj);

                                that.response.markdown += '\n示例请求参数：\n\n';
                                that.response.markdown += '```\n' + that.JsonFormat(JSON.parse(that.request.body)) +
                                    '\n```\n\n';
                            } catch (error) {
                                that.response.markdown += '```\n' + that.request.body + '\n```\n\n';
                            }
                            break;
                        case 'application/x-www-form-urlencoded;':
                            that.response.markdown += '|字段|类型|必填|说明|\n';
                            that.response.markdown += '|-|-|-|-|\n';
                            var arr = that.request.body.split('&');
                            for (var index in arr) {
                                var item = arr[index].split('=');
                                if (item.length == 2) {
                                    var type = typeof(item[1]);
                                    that.response.markdown += '|' + item[0] + '|' + type + '|是|暂无备注|\n';
                                }
                            }
                            if (arr.length == 0 || arr.length == 1 && !arr[0]) {
                                that.response.markdown += '|-|-|-|-|\n';
                            }
                            that.response.markdown += '\n示例请求参数：\n\n';
                            try {
                                that.response.markdown += '```\n' + that.JsonFormat(JSON.parse(that.request.body)) +
                                    '\n```\n\n';
                            } catch (error) {
                                that.response.markdown += '```\n' + that.request.body + '\n```\n\n';
                            }
                            break;
                        default:
                            try {
                                var obj = JSON.parse(that.request.body);
                                that.response.markdown += '|字段|类型|必填|说明|\n';
                                that.response.markdown += '|-|-|-|-|\n';
                                that.response.markdown += that.getJsonMarkdown(obj);

                                that.response.markdown += '\n示例请求参数：\n\n';
                                that.response.markdown += '```\n' + that.JsonFormat(JSON.parse(that.request.body)) +
                                    '\n```\n\n';
                            } catch (error) {
                                that.response.markdown += '\n示例请求参数：\n\n';
                                that.response.markdown += '```\n' + that.request.body + '\n```\n\n';
                            }
                    }
                } else {
                    that.response.markdown += '请求参数请查看URL\n\n';
                }
                that.response.markdown += '#### 四、更多参数\n\n';
                that.response.markdown += 'Headers:\n\n';
                that.response.markdown += '```\n' + that.request.header + '\n```\n\n';
                if (that.request.cookie) {
                    that.response.markdown += 'Cookies:\n\n';
                    that.response.markdown += '```\n' + that.request.cookie + '\n```\n\n';
                }
                that.response.markdown += '#### 五、返回数据\n\n';
                try {
                    that.response.markdown += '|字段|类型|固定返回|示例值|说明|\n';
                    that.response.markdown += '|-|-|-|-|-|\n';
                    var obj = JSON.parse(that.response.body);
                    that.response.markdown += that.getJsonMarkdown(obj);
                } catch (error) {
                    that.response.markdown += that.response.body + '\n\n';
                }
                that.response.markdown += '\n示例返回结果：\n\n';
                that.response.markdown += '```\n' + that.response.body + '\n```\n\n';
                that.response.markdown += '#### 六、测试用例\n\n';
                that.response.markdown += location.href;

            },
            //解析在线版本返回的数据
            decodeResponseDataOnline(response) {
                var that = this;
                if (response.data.data.body.length > that.maxResponseLength) {
                    that.response.body = '返回文本超长，为了Tester的性能考虑，这里不予显示。';
                } else {
                    try {
                        that.response.body = unescape(that.JsonFormat(JSON.parse(response.data.data.body)))
                    } catch (error) {
                        that.response.body = that.html2Escape(response.data.data.body);
                    }
                }
                try {
                    that.response.detail = unescape(that.JsonFormat(response.data.data.detail))
                } catch (error) {
                    that.response.detail = that.html2Escape(response.data.data.detail);
                }
                try {
                    that.response.header = unescape(that.JsonFormat(JSON.parse(response.data.data.header)))
                } catch (error) {
                    that.response.header = that.html2Escape(response.data.data.header);
                }
                that.response.httpcode = response.data.data.detail.http_code;
                location.href = "/#/" + response.data.data.key;

                that.response.markdown = '';
                that.response.markdown += '## xxx API接口文档\n\n';
                that.response.markdown += '> 本文档由 [Tester](https://tester.hamm.cn) 自动生成，最后修改时间 ' + that.getNowDateTime() +
                    '\n\n';

                that.response.markdown += '#### 一、接口说明\n\n';
                that.response.markdown += '你可以在这里对接口进行一些简单的描述\n\n';
                that.response.markdown += '#### 二、请求方式\n\n';
                that.response.markdown += '```' + that.request.method + ' ' + that.request.url + '```\n\n';
                that.response.markdown += '#### 三、请求参数\n\n';
                if (that.request.method != 'GET') {
                    switch (that.factory.contentType) {
                        case 'application/json;':
                            try {
                                var obj = JSON.parse(that.request.body);
                                that.response.markdown += '|字段|类型|必填|说明|\n';
                                that.response.markdown += '|-|-|-|-|\n';
                                that.response.markdown += that.getJsonMarkdown(obj);

                                that.response.markdown += '\n示例请求参数：\n\n';
                                that.response.markdown += '```\n' + that.JsonFormat(JSON.parse(that.request.body)) +
                                    '\n```\n\n';
                            } catch (error) {
                                that.response.markdown += '```\n' + that.request.body + '\n```\n\n';
                            }
                            break;
                        case 'application/x-www-form-urlencoded;':
                            that.response.markdown += '|字段|类型|必填|说明|\n';
                            that.response.markdown += '|-|-|-|-|\n';
                            var arr = that.request.body.split('&');
                            for (var index in arr) {
                                var item = arr[index].split('=');
                                if (item.length == 2) {
                                    var type = typeof(item[1]);
                                    that.response.markdown += '|' + item[0] + '|' + type + '|是|暂无备注|\n';
                                }
                            }
                            if (arr.length == 0 || arr.length == 1 && !arr[0]) {
                                that.response.markdown += '|-|-|-|-|\n';
                            }
                            that.response.markdown += '\n示例请求参数：\n\n';
                            try {
                                that.response.markdown += '```\n' + that.JsonFormat(JSON.parse(that.request.body)) +
                                    '\n```\n\n';
                            } catch (error) {
                                that.response.markdown += '```\n' + that.request.body + '\n```\n\n';
                            }
                            break;
                        default:
                            try {
                                var obj = JSON.parse(that.request.body);
                                that.response.markdown += '|字段|类型|必填|说明|\n';
                                that.response.markdown += '|-|-|-|-|\n';
                                that.response.markdown += that.getJsonMarkdown(obj);

                                that.response.markdown += '\n示例请求参数：\n\n';
                                that.response.markdown += '```\n' + that.JsonFormat(JSON.parse(that.request.body)) +
                                    '\n```\n\n';
                            } catch (error) {
                                that.response.markdown += '\n示例请求参数：\n\n';
                                that.response.markdown += '```\n' + that.request.body + '\n```\n\n';
                            }
                    }
                } else {
                    that.response.markdown += '请求参数请查看URL\n\n';
                }
                that.response.markdown += '#### 四、更多参数\n\n';
                that.response.markdown += 'Headers:\n\n';
                that.response.markdown += '```\n' + that.request.header + '\n```\n\n';
                if (that.request.cookie) {
                    that.response.markdown += 'Cookies:\n\n';
                    that.response.markdown += '```\n' + that.request.cookie + '\n```\n\n';
                }
                that.response.markdown += '#### 五、返回数据\n\n';
                try {
                    that.response.markdown += '|字段|类型|固定返回|示例值|说明|\n';
                    that.response.markdown += '|-|-|-|-|-|\n';
                    var obj = JSON.parse(that.response.body);
                    that.response.markdown += that.getJsonMarkdown(obj);
                } catch (error) {
                    that.response.markdown += that.response.body + '\n\n';
                }
                that.response.markdown += '\n示例返回结果：\n\n';
                that.response.markdown += '```\n' + that.response.body + '\n```\n\n';
                that.response.markdown += '#### 六、测试用例\n\n';
                that.response.markdown += location.href;

            },
            getJsonMarkdown(obj, prefix = "", isArray = false) {
                try {
                    var that = this;
                    var _markdown = '';
                    if (isArray) {
                        for (var key in obj) {
                            var type = typeof(obj[key]);
                            if (type == 'object') {
                                if (obj[key] instanceof Array) {
                                    _markdown += '|' + prefix + key + '|array|是|[]|暂无备注|\n';
                                    _markdown += that.getJsonMarkdown(obj[key], prefix + key + ".", true);
                                } else if (obj[key] instanceof Object) {
                                    // _markdown += '|' + prefix + key + '|object|是|{}|暂无备注|\n';
                                    _markdown += that.getJsonMarkdown(obj[key], prefix);
                                } else {
                                    _markdown += '|' + prefix + key + '|array|是|[]|暂无备注|\n';
                                }
                            }
                            break;
                        }
                    } else {
                        for (var key in obj) {
                            var type = typeof(obj[key]);
                            if (obj[key] == null || obj[key] == undefined) {
                                type = 'null';
                            }
                            if (type == 'object') {
                                if (obj[key] instanceof Array) {
                                    _markdown += '|' + prefix + key + '|array|是|[]|暂无备注|\n';
                                    _markdown += that.getJsonMarkdown(obj[key], prefix + key + ".", true);
                                } else if (obj[key] instanceof Object) {
                                    _markdown += '|' + prefix + key + '|object|是|{}|暂无备注|\n';
                                    _markdown += that.getJsonMarkdown(obj[key], prefix + key + ".");
                                } else {
                                    _markdown += '|' + prefix + key + '|array|是|[]|暂无备注|\n';
                                }
                            } else {
                                _markdown += '|' + prefix + key + '|' + type + '|是|' + obj[key] + '|暂无备注|\n';
                            }
                        }
                    }
                    return _markdown;
                } catch (error) {
                    console.log(error)
                }
            },
            get_url_params() { //获取url里面的id参数
                var arr = window.location.href.split('/#/');
                if (arr.length == 2) {
                    return arr[1];
                } else {
                    return false;
                }
            },
            getNowDateTime: function() {
                var now = new Date(),
                    y = now.getFullYear(),
                    m = now.getMonth() + 1,
                    d = now.getDate();
                return y + "-" + (m < 10 ? "0" + m : m) + "-" + (d < 10 ? "0" + d : d) + " " + now.toTimeString().substr(
                    0, 8);
            },
            encodeUTF8(s) {
                var i, r = [],
                    c, x;
                for (i = 0; i < s.length; i++)
                    if ((c = s.charCodeAt(i)) < 0x80) r.push(c);
                    else if (c < 0x800) r.push(0xC0 + (c >> 6 & 0x1F), 0x80 + (c & 0x3F));
                else {
                    if ((x = c ^ 0xD800) >> 10 == 0) //对四字节UTF-16转换为Unicode
                        c = (x << 10) + (s.charCodeAt(++i) ^ 0xDC00) + 0x10000,
                        r.push(0xF0 + (c >> 18 & 0x7), 0x80 + (c >> 12 & 0x3F));
                    else r.push(0xE0 + (c >> 12 & 0xF));
                    r.push(0x80 + (c >> 6 & 0x3F), 0x80 + (c & 0x3F));
                };
                return r;
            },
            sha1(s) {
                var data = new Uint8Array(this.encodeUTF8(s))
                var i, j, t;
                var l = ((data.length + 8) >>> 6 << 4) + 16,
                    s = new Uint8Array(l << 2);
                s.set(new Uint8Array(data.buffer)), s = new Uint32Array(s.buffer);
                for (t = new DataView(s.buffer), i = 0; i < l; i++) s[i] = t.getUint32(i << 2);
                s[data.length >> 2] |= 0x80 << (24 - (data.length & 3) * 8);
                s[l - 1] = data.length << 3;
                var w = [],
                    f = [

                        function() {
                            return m[1] & m[2] | ~m[1] & m[3];
                        },

                        function() {
                            return m[1] ^ m[2] ^ m[3];
                        },

                        function() {
                            return m[1] & m[2] | m[1] & m[3] | m[2] & m[3];
                        },

                        function() {
                            return m[1] ^ m[2] ^ m[3];
                        }
                    ],
                    rol = function(n, c) {
                        return n << c | n >>> (32 - c);
                    },
                    k = [1518500249, 1859775393, -1894007588, -899497514],
                    m = [1732584193, -271733879, null, null, -1009589776];
                m[2] = ~m[0], m[3] = ~m[1];
                for (i = 0; i < s.length; i += 16) {
                    var o = m.slice(0);
                    for (j = 0; j < 80; j++)
                        w[j] = j < 16 ? s[i + j] : rol(w[j - 3] ^ w[j - 8] ^ w[j - 14] ^ w[j - 16], 1),
                        t = rol(m[0], 5) + f[j / 20 | 0]() + m[4] + w[j] + k[j / 20 | 0] | 0,
                        m[1] = rol(m[1], 30), m.pop(), m.unshift(t);
                    for (j = 0; j < 5; j++) m[j] = m[j] + o[j] | 0;
                };
                t = new DataView(new Uint32Array(m).buffer);
                for (var i = 0; i < 5; i++) m[i] = t.getUint32(i << 2);

                var hex = Array.prototype.map.call(new Uint8Array(new Uint32Array(m).buffer), function(e) {
                    return (e < 16 ? "0" : "") + e.toString(16);
                }).join("");
                return hex;
            },
            getRandId() {
                return this.sha1(new Date().valueOf() + "." + Math.random());
            },
            JsonFormat(json) {
                if (typeof json != 'string') {
                    json = JSON.stringify(json, undefined, 4);
                }
                return json;
            },
            html2Escape(sHtml) {
                return sHtml.replace(/[<>&"]/g, function(c) {
                    return {
                        '<': '&lt;',
                        '>': '&gt;',
                        '&': '&amp;',
                        '"': '&quot;'
                    } [c];
                });
            },
            escape2Html(str) {
                var arrEntities = {
                    'lt': '<',
                    'gt': '>',
                    'nbsp': ' ',
                    'amp': '&',
                    'quot': '"'
                };
                return str.replace(/&(lt|gt|nbsp|amp|quot);/ig, function(all, t) {
                    return arrEntities[t];
                });
            }
        }
    })
</script>

</html>