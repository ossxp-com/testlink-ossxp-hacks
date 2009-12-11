<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Filename $RCSfile: texts.php,v $
 * @version $Revision: 1.2 $
 * @modified $Date: 2009/03/05 18:38:50 $ by $Author: schlundus $
 * @author Martin Havlat and reviewers from TestLink Community
 *
 * --------------------------------------------------------------------------------------
 *
 * Scope:
 * English (en_GB) texts for help/instruction pages. Strings for dynamic pages
 * are stored in strings.txt pages.
 *
 * Here we are defining GLOBAL variables. To avoid override of other globals
 * we are using reserved prefixes:
 * $TLS_help[<key>] and $TLS_help_title[<key>]
 * or
 * $TLS_instruct[<key>] and $TLS_instruct_title[<key>]
 *
 *
 * Revisions history is not stored for the file
 *
 * ------------------------------------------------------------------------------------ */


$TLS_htmltext_title['assignReqs']	= "分配需求给测试用例";
$TLS_htmltext['assignReqs'] 		= "<h2>目的:</h2>
<p>用户可以设置测试用例集和需求规约之间的关系. 设计者可以把此处的测试用例集和需求规约一一关联
.例如:一个测试用例可以被关联到零个、一个、多个测试用例集,反之亦然.
Such traceability matrix helps to investigate test coverage
of requirements and find out which ones successfully failed during a testing. This
analyse serves as input for the next planning.</p>

<h2>Get Started:</h2>
<ol>
	<li>Choose an Test Case in tree at the left. The combo box with list of Requirements
	Specifications is shown at the top of workarea.</li>
	<li>Choose a Requirements Specification Document if more once defined. 
	TestLink automatically reload the page.</li>
	<li>A middle block of workarea lists all requirements (from choosen Specification), which
	are connected with the test case. Bottom block 'Available Requirements' lists all
	requirements which have not relation
	to the current test case. A designer could mark requirements which are covered by this
	test case and then click the button 'Assign'. These new assigned test case are shown in
	the middle block 'Assigned Requirements'.</li>
</ol>";


// --------------------------------------------------------------------------------------
$TLS_htmltext_title['editTc']	= "测试规范";
$TLS_htmltext['editTc'] 		= "<h2>目的:</h2>
<h2>目的:</h2>
<p> <i>测试规范</i> 允许用户查看和编辑所有现在有的测试案例集" .
		"<i>测试用例集</i> 和 <i>测使用例</i>. 同一个测试案例可以保留多个版本.".
		"并且所有以前的历史版本都将被保留并在这里可以查看和管理.</p>

<h2>开始:</h2>
<ol>
	<li>从左边的导航树(顶级树)中找出你的测试项目. <i>注意: " .
	"你永远可以从右上角的下拉菜单选择改变当前的测试项目." .
	".</i></li>
	<li>点击 <b>新测试案例子集</b>将创建一个新的测试案例子集. " .
	"测试案例子集可以为你的测试文档归类 归类可以是按照你的需要来(功能/非功能, 测试案例, 产品部件, 产品功能, 更改需求, 等等)." .
	"测试案例子集积聚了一类相关的测试, 他们用在同一个功能范围, 具有相同的系统配置信息等 他们还可能共同和其他一些文档资料, 测试局限性, 或者其他信息. 通常这些信息是该测试案例子集所共同具有的. 他们组成了一个测试案例的文件夹的概念 测试案例子集是可以扩充的文件夹. 用户可以在同一个测试计划里移动或者复制它们.同时, 他们可以作为一个整体(包括其中的测试案例)输出或者输入到其他格式." .".</li>
	<li>在导航树中选择一个刚创建的新的测试子集" .
	"然后点击<b>创建测使用例</b>. 就可以在这个子集里创建一个新的案例." .
	"一个测试案例定义了一个单一的测试过程 它包括测试的环境, 步骤, 期望的结果, 已经可以自定义的栏目 在测试计划里(参见用户手册), 还可以给测试案例增添一个" .
	"<b>关键字</b> 以方便跟踪查询.</li>
	<li>从左边的导航树里选择和编辑数据. 测试案例本身可以保存多个版本和修改历史.</li>
	<li>测试案例编写完毕后, 你可以给把它的上级测试规范关联到 <span class=\"help\" onclick=
	\"javascript:open_help_window('glosary','$locale');\">Test Plan</span> .</li>
</ol>

    <p>TL测试管理系统帮你整理测试案例, 分类成为不同的测试案例子集. 测试案例子集还可以包含更下级的测试案例子集. 
       你因此可以组织成一个树状体系. 最后可以打印成册." ."</p>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['searchTc']	= "Test Case Search Page";
$TLS_htmltext['searchTc'] 		= "<h2>Purpose:</h2>

<p>Navigation according to keywords and/or searched strings. The search is not
case sensitive. Result include just test cases from actual Test Project.</p>

<h2>To search:</h2>

<ol>
	<li>Write searched string to an appropriate box. Left blank unused fields in form.</li>
	<li>Choose required keyword or left value 'Not applied'.</li>
	<li>Click the Search button.</li>
	<li>All fulfilled test cases are shown. You can modify test cases via 'Title' link.</li>
</ol>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['printTestSpec']	= "Print Test Specification"; //printTC.html
$TLS_htmltext['printTestSpec'] 			= "<h2>Purpose:</h2>
<p>From here you can print a single test case, all the test cases within a test suite,
or all the test cases in a test project or plan.</p>
<h2>Get Started:</h2>
<ol>
<li>
<p>Select the parts of the test cases you want to display, and then click on a test case, test suite, or the test project.
A printable page will be displayed.</p>
</li>
<li><p>Use the \"Show As\" drop-box in the navigation pane to specify whether you want the information displayed as HTML or in a
Microsoft Word document. See <span class=\"help\" onclick=\"javascript:open_help_window('printFilter',
'{$locale}');\">help</span> for more information.</p>
</li>
<li><p>Use your browser's print functionality to actually print the information.<br />
 <i>Note: Make sure to only print the right-hand frame.</i></p></li>
</ol>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['reqSpecMgmt']	= "Requirements Specification Design"; //printTC.html
$TLS_htmltext['reqSpecMgmt'] 			= "<p>You can manage Requirement Specification documents.</p>

<h2>Requirements Specification</h2>

<p>Requirements are grouped by <b>Requirements Specification document</b>, which is related to
Test Project.<br /> TestLink doesn't support (yet) versions for both Requirements Specification
and Requirements itself. So, version of document should be added after
a Specification <b>Title</b>.
An user can add simple description or notes to <b>Scope</b> field.</p>

<p><b><a name='total_count'>Overwritten count of REQs</a></b> serves for
evaluation Req. coverage in case that not all requirements are added to TestLink.
The value <b>0</b> means that current count of requirements is used
for metrics.</p>
<p><i>E.g. SRS includes 200 requirements but only 50 are added in TestLink. Test
coverage is 25% (if all these added requirements will be tested).</i></p>

<h2><a name='req'>Requirements</a></h2>

<p>Click on title of a created Requirements Specification, if none exists click on the project node to create one. You can create, edit, delete
or import requirements for the document. Each requirement has title, scope and status.
Status should be 'Normal' or 'Not testable'. Not testable requirements are not counted
to metrics. This parameter should be used for both unimplemented features and
wrong designed requirements.</p>

<p>You can create new test cases for requirements by using multi action with checked
requirements within the specification screen. These Test Cases are created into Test Suite
with name defined in configuration <i>(default is: \$tlCfg->req_cfg->default_testsuite_name =
'Test suite created by Requirement - Auto';)</i>. Title and Scope are copied to these Test cases.</p>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['keywordsAssign']	= "Keyword Assignment";
$TLS_htmltext['keywordsAssign'] 			= "<h2>Purpose:</h2>
<p>The Keyword Assignment page is the place where users can batch
assign keywords to the existing Test Suite or Test Case</p>

<h2>To Assign Keywords:</h2>
<ol>
	<li>Select a Test Suite, or Test Case on the tree view
		on the left.</li>
	<li>The top most box that shows up on the right hand side will
		allow you to assign available keywords to every single test
		case.</li>
	<li>The selections below allow you to assign cases at a more
		granular level.</li>
</ol>

<h2>Important Information Regarding Keyword Assignments in Test Plans:</h2>
<p>Keyword assignments you make to the specification will only effect test cases
in your Test plans if and only if the test plan contains the latest version of the Test case.
Otherwise if a test plan contains older versions of a test case, assignments you make
now WILL NOT appear in the test plan.
</p>
<p>TestLink uses this approach so that older versions of test cases in test plans are not effected
by keyword assignments you make to the most recent version of the test case. If you want your
test cases in your test plan to be updated, first verify they are up to date using the 'Update
Modified Test Cases' functionality BEFORE making keyword assignments.</p>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['executeTest']	= "Test Case Execution";
$TLS_htmltext['executeTest'] 		= "<h2>Purpose:</h2>

<p>Allows user to execute Test cases. User can assign Test result
to Test Case for Build. See help for more information about filter and settings " .
		"(click on the question mark icon).</p>

<h2>Get started:</h2>

<ol>
	<li>User must have defined a Build for the Test Plan.</li>
	<li>Select a Build from the drop down box and the \"Apply\" button in the navigation pane.</li>
	<li>Click on a test case in the tree menu.</li>
	<li>Fill out the test case result and any applicable notes or bugs.</li>
	<li>Save results.</li>
</ol>
<p><i>Note: TestLink must be configurated to collaborate with your Bug tracker 
if you would like to create/trace a problem report directly from the GUI.</i></p>";

// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['showMetrics']	= "测试报告和统计数据";
$TLS_htmltext['showMetrics'] 		= "<p>一个测试计划的报告" .
		"(在导航条里定义了). 这个测试计划可能个当前执行的测试计划不同. 可以选择的格式有:</p>
<ul>
<li><b>正常</b> - 报告显示为网页格式</li>
<li><b>MS Excel</b> - 报告输出为 Microsoft Excel</li>
<li><b>HTML Email</b> -报告以邮件形式发送到用户的心想 </li>
<li><b>图表</b> - 报告以图表形式出现(flash 技术)</li>
</ul>

<p>打印键将激活当前报告的打印功能.</p>
<p>报表有多种形式. 其格式, 目的和功能如下.</p>

<h3>通用测试统计报告</h3>
<p>该页只显示测试计划里测试用例集,所有者, 关键字的最新状态.
'当前状态' 是指最近的测试版本的测试执行状态.例如. 一个测试案例在多个构建版本上执行过. 这里只显示最新版本的结果.</p>

<p>'最终测试结果'是许多报告里用的一个概念. 它是这样定义的:</p>
<ul>
<li>构建版本加入到测试计划里的先后顺序决定了哪个构建版本是最新的. 最新版本的测试结果比旧版本的测试结果优先. 例如, 如果你在版本1里记录了一个测试用例的测试结果是’失败’. 你在版本2里记录同一个测试用例的测试结果是'通过',则最终的测试结果是 '通过'.</li>
<li>如果同一个测试用例在同一个构建版本上执行了多次. 那么最后一次执行的结果优先. 例如. 如果版本 3 发布了. 你的团队里的 tester 1 在 2:00pm 报告结果为'通过',而 tester 2 在 3:00pm 报告结果为'失败'- 则最终结果显示为'失败'.</li>
<li>在某个版本里显示为'未执行'的测试用例不会覆盖上一次的测试结果. 例如, 如果你在版本 1 中测试结束后记录为'通过', 在版本2里还没有执行, 则显示的最终结果是'通过'.
</li>
</ul>
<p>显示的列表:</p>
<ul>
	<li><b>按顶级测试案例子集</b>
	表中列出顶级的测试案例. 总案例数目, 通过数目, 失败数目, 受阻数目, 未执行数目, 顶级测试子级和下级子集的百分比.</li>
	<li><b>按关键字</b>
	表中列出当前测试计划里所有测试案例里的关键词, 以及对应的测试结果.</li>
	<li><b>按测试者</b>
	列出当前测试计划里分派给各用户的测试案例. 未分配给用户的归类到 '(未分派)unassigned' 栏里.</li>
</ul>

<h3>总体版本状态</h3>
<p>列出各个版本的执行结果. 对于每一个版本, 有总测试案例数, 总通过数, 通过的比例, 总失败数, 失败的比例, 总受阻的数目, 受阻的比例, 未执行的总数, 未执行的比例. 如果一个测试案例在同一个构建版本上执行了多次, 则最近一次的结果才计入统计.</p>

<h3>查询统计</h3>
<p>该报表包括一个查询输入表, 一个查询结果页. 查询输入表有四个按钮. 每个按钮的缺省值设置为查询可以包括的最大范围. 用户可以更改 按钮以缩小查询范围. 可以按分派人, 关键词, 子类, 版本等组合过滤.</p>

<ul>
<li><b>关键字</b>可以选择 0->1 个关键词. 系统缺省的设置是不选. 如果该关键词不被选中, 则测试规范里和关键词管理 页不管有没有分配该关键词的所有测试案例都包括. 一个关键词进入一个测试案例后, 将传播到该测试案例所属于的所有的 测试计划, 以及该案例的所有版本. 如果你只关心有特定的关键词的测试结果, 你要改变控制按钮的值. </li>
<li><b>所有者</b> 可以选择 0->1 个主人. 系统缺省的设置是不选. 如果不选. 则所有案例都选择,不管测试任务分派给谁了. 目前还没有搜索'未指派'执行人的测试案例的功能. 主人是通过 '指派测试任务(Assign Test Case execution)'页来实现的, 而且是每个测试计划都要单独做的. 如果你关心工作是谁做的, 你要修改这个按钮的值.</li>
<li><b>顶级子集</b>可以选择 0->n 级测试子集. 缺省状态是所有子集.
    只有被选取的子集才出现在查询结果中.如果你只关心某个子集,你可以修改这项控制.</li>
<li><b>版本</b> 可以选择 1->n 个版本. 缺省状态是选择所有子集. 在做统计的时候只把你选取的版本同时是执行过的测试结果计算在内. 例如, 如果你只想看到在过去的三个版本上做过多少次测试, 你可以修改这个按钮. 关键词, 所有者, 顶级子集的三项过滤决定了计入统计数据中的案例数目. 例如, 如果你选择了 主人 = '张三', 关键词 = '优先 1', 以及所有的子集- 那末只有分派给张三的优先级为 1 的测试案例被计算在内. 报表中看到的测试案例的总数目会随着这三个过滤过滤按钮给出的条件的不同而不同. 版本过滤只对'通过', '失败', '受阻', 或者'未执行' 的案例有过滤作用. 参见上面关于最后测试结果的说明.</li>
</ul>
<p>点击"提交"按钮启动查询和输出显示的文件</p>

<p>查询报表页将显示: </p>
<ol>
<li>用于创建报表的查询参数</li>
<li>测试计划的全部参数</li>
<li>显示了一个子集里所有执行的结果和按(总和/通过/失败/受阻/未执行)分类的结果. 如果一个测试用例在多个版本上执行过多次, 各次执行的结果都会显示出来. 然而在该子集的总结里, 只有选定版本的测试结果才会被显示出来.</li>
</ol>

<h3>受阻, 失败, 未执行的测试用例报表</h3>
<p>这些报表显示当前受阻, 失败或者未执行的测试用例. 使用的数据是'最终测试结果' (见前面通用测试统计段落). 如果系统整和了错误跟踪系统, 那些受阻和失败的测试案例报告还将显示错误编号.</p>

<h3>测试报告</h3>
<p>阅读各个版本的每个测试案例的结果. 如果一个测使用例被执行过多次, 只显示最近的结果. 如果数据很多, 建议输出到excel表格中来阅读.
</p>

<h3>图表 - 通用测试计划图表</h3>
<p>
所有四个图表都使用'最终测试结果'. 图表有动画显示, 方便查看当前测试计划的统计结果.四个报表提供了:
</p>
<ul><li>通过/失败/受阻/未执行的测试用例的分布饼图</li>
<li>按关键词显示的图表</li>
<li>按所有者显示的图表</li>
<li>按顶级子集显示的图表</li>
</ul>
<p>图表中的方块都有颜色标记, 方便用户识别出通过,失败, 受阻, 未执行的测使用例的大概数目.
</p>
<p><i>该报告中的图表显示需要你安装一个插件(by http://www.maani.us).</i></p>

<h3>每个测试用例报告的错误总数</h3>
<p>该报表显示了每个测试用例所发现的所有错误. 包括全部项目中的所有错误. 该报表只有在和错误跟踪系统整合了以后才可见.</p>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['planAddTC']	= "Add / Remove Test cases to Test Plan"; // testSetAdd
$TLS_htmltext['planAddTC'] 			= "<h2>Purpose:</h2>
<p>Allows user (with lead level permissions) to add or remove test cases into a Test plan.</p>

<h2>To add or remove Test cases:</h2>
<ol>
	<li>Click on a test suite to see all of its test suites and all of its test cases.</li>
	<li>When you are done click the 'Add / Remove Test Cases' button to add or remove the test cases.
		Note: Is not possible to add the same test case multiple times.</li>
</ol>";

// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['tc_exec_assignment']	= "Assign Testers to test execution";
$TLS_htmltext['tc_exec_assignment'] 		= "<h2>Purpose</h2>
<p>This page allows test leaders to assign users to particular tests within the Test Plan.</p>

<h2>Get Started</h2>
<ol>
	<li>Choose a Test case or Test Suite to test.</li>
	<li>Select a planned tester.</li>
	<li>Press button to submit assignement.</li>
	<li>Open execution page to verify assignment. You can set-up a filter for users.</li>
</ol>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['planUpdateTC']	= "Update Test Cases in the Test Plan";
$TLS_htmltext['planUpdateTC'] 		= "<h2>Purpose</h2>
<p>This page allows update Test case to a newer (different) version in  the case that Test
Specification is changed. It often happens that some functionality is clarified during testing." .
		" User modifies Test Specification, but changes needs to propagate to Test Plan too. Otherwise Test" .
		" plan holds original version to be sure, that results refer to the correct text of a Test case.</p>

<h2>Get Started</h2>
<ol>
	<li>Choose a Test case or Test Suite to test.</li>
	<li>Choose a new version from bombo boxmenu for particular Test case.</li>
	<li>Press button 'Update Test plan' to submit changes.</li>
	<li>To verify: Open execution page to view text of the test case(s).</li>
</ol>";


// ------------------------------------------------------------------------------------------
$TLS_htmltext_title['test_urgency']	= "Specify tests with high or low urgency";
$TLS_htmltext['test_urgency'] 		= "<h2>Purpose</h2>
<p>TestLink allows set urgency of Test Suite to affect a testing Priority of test cases. 
		Test priority depends on both Importance of Test cases and Urgency defined in 
		the Test Plan. Test leader should specify a set of test cases that could be tested
		at first. It helps to assure that testing will cover the most important tests
		also under time pressure.</p>

<h2>Get Started</h2>
<ol>
	<li>Choose a Test Suite to set urgency of a product/component feature in navigator
	on the left side of window.</li>
	<li>Choose a urgency level (high, medium or low). Medium is default. You can
	decrease priority for untouched parts of product and increase for components with
	significant changes.</li>
	<li>Press the button 'Save' to submit changes.</li>
</ol>
<p><i>For example, a Test case with a High importance in a Test suite with Low urgency " .
		"will be Medium priority.</i>";


// ------------------------------------------------------------------------------------------

?>
