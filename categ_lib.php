<?php
/** 
 * $Header: /cvsroot/bitweaver/_bit_categories/categ_lib.php,v 1.3.2.10 2005/07/26 15:50:03 drewslater Exp $
 *
 * Categories support class
 *
 * @package  categories
 */

/** 
 * Categories support class
 *
 * @package  categories
 * @subpackage  CategLib
 */
class CategLib extends BitBase {

	function CategLib() {
		BitBase::BitBase();
	}

	function list_all_categories($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid, $pRootCategoryId=NULL ) {
		$cats = $this->get_object_categories($type, $objid);

		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$bindvals=array($findesc,$findesc);
			$mid = " where (UPPER(`name`) like ? or UPPER(`description`) like ?)";
		} elseif( $pRootCategoryId ) {
			$mid = " where `parent_id`=? ";
			$bindvals = $pRootCategoryId;
		} else {
			$bindvals=array();
			$mid = "";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_categories` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_categories` $mid";
		$result = $this->query($query,$bindvals,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvals);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if (in_array($res["category_id"], $cats)) {
				$res["incat"] = 'y';
			} else {
				$res["incat"] = 'n';
			}
			$categpath = $this->get_category_path( $res );
			$res['root_category_id'] = $categpath['root_category_id'];
			$res["categpath"] = $categpath['linked'];
			$res["categpath_static"] = $categpath['static'];
			$ret[$categpath['linked']] = $res;
		}
		ksort($ret);
		$retval = array();
	    $retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_category_path_admin($category_id) {

		$info = $this->get_category($category_id);
		$path = '<a class="categpath" href="'.CATEGORIES_PKG_URL.'admin/index.php?parent_id=' . $info["category_id"] . '">' . ($info["name"]) . '</a>';

		while ($info["parent_id"] != 0) {
			$info = $this->get_category($info["parent_id"]);
			$path = '<a class="categpath" href="'.CATEGORIES_PKG_URL.'admin/index.php?parent_id=' . $info["category_id"] . '">' . ($info["name"]) . '</a>' . ' &raquo; ' . $path;
		}

		return $path;
	}

	function get_category_path_browse($category_id) {

		$info = $this->get_category($category_id);
		$path = '<a class="categpath" href="'.CATEGORIES_PKG_URL.'index.php?parent_id=' . $info["category_id"] . '">' . ($info["name"]) . '</a>';

		while ($info["parent_id"] != 0) {
			$info = $this->get_category($info["parent_id"]);
			$path = '<a class="categpath" href="'.CATEGORIES_PKG_URL.'index.php?parent_id=' . $info["category_id"] . '">' . ($info["name"]) . '</a>' . ' &raquo; ' . $path;
		}

		return $path;
	}

	function get_category( $pCategoryId ) {
		static $catCache;

		if( empty( $catCache[$pCategoryId] ) ) {
			$query = "select * from `".BIT_DB_PREFIX."tiki_categories` where `category_id`=?";

			$result = $this->query($query,array((int) $pCategoryId));

			if (!$result->numRows())
				return false;

			$res = $result->fetchRow();
			$catCache[$pCategoryId] = $res;
		} else {
			$res = $catCache[$pCategoryId];
		}
		return $res;
	}

	function remove_category($category_id) {
		// Delete the category
		$query = "delete from `".BIT_DB_PREFIX."tiki_categories` where `category_id`=?";

		$result = $this->query($query,array((int) $category_id));
		// Remove objects for this category
		$query = "select `cat_object_id` from `".BIT_DB_PREFIX."tiki_category_objects` where `category_id`=?";
		$result = $this->query($query,array((int) $category_id));

		while ($res = $result->fetchRow()) {
			$object = $res["cat_object_id"];

			$query2 = "delete from `".BIT_DB_PREFIX."tiki_categorized_objects` where `cat_object_id`=?";
			$result2 = $this->query($query2,array($object));
		}

		$query = "delete from `".BIT_DB_PREFIX."tiki_category_objects` where `category_id`=?";
		$result = $this->query($query,array((int) $category_id));
		$query = "select `category_id` from `".BIT_DB_PREFIX."tiki_categories` where `parent_id`=?";
		$result = $this->query($query,array((int) $category_id));

		while ($res = $result->fetchRow()) {
			// Recursively remove the subcategory
			$this->remove_category($res["category_id"]);
		}

		return true;
	}

	function update_category($category_id, $name, $description, $parent_id) {
		$query = "update `".BIT_DB_PREFIX."tiki_categories` set `name`=?, `parent_id`=?, `description`=? where `category_id`=?";
		$result = $this->query($query,array($name,(int) $parent_id,$description,(int) $category_id));
	}

	function add_category($parent_id, $name, $description, $pCatId=NULL ) {
		$bindVars = array($name,$description,(int) $parent_id, 0 );
		$idVal = '';
		$idName = '';
		if( isset( $pCatId ) && is_numeric( $pCatId ) ) {
			array_push( $bindVars, $pCatId );
			$idVal = ',?';
			$idName = ', `category_id` ';
		}
		$query = "insert into `".BIT_DB_PREFIX."tiki_categories` ( `name`,`description`,`parent_id`,`hits` $idName ) values (?,?,?,? $idVal )";
		$result = $this->query($query, $bindVars );
	}

	function is_categorized($type, $pObjId) {
		$ret = FALSE;
		if( !empty( $pObjId ) && is_numeric( $pObjId ) ) {
			$query = "SELECT `cat_object_id`
					  FROM `".BIT_DB_PREFIX."tiki_categorized_objects`
					  WHERE `object_type`=? and `object_id`=?";
			$bindvars=array($type,$pObjId);
			settype($bindvars["1"],"string");
			$result = $this->query($query,$bindvars);
			if ($result->numRows()) {
				$res = $result->fetchRow();
				$ret = $res["cat_object_id"];
			}
		}
		return $ret;
	}

	function add_categorized_object($type, $obj_id, $description, $name, $href) {
		$description = strip_tags($description);

		$name = strip_tags($name);
		$now = date("U");
		$query = "insert into `".BIT_DB_PREFIX."tiki_categorized_objects`(`object_type`,`object_id`,`description`,`name`,`href`,`created`,`hits`)
    values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($type,(string) $obj_id,$description,$name,$href,(int) $now,0));
		$query = "select `cat_object_id` from `".BIT_DB_PREFIX."tiki_categorized_objects` where `created`=? and `object_type`=? and `object_id`=?";
		$id = $this->getOne($query,array((int) $now,$type,(string) $obj_id));
		return $id;
	}

	function categorize($cat_object_id, $category_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_category_objects` where `cat_object_id`=? and `category_id`=?";
		$result = $this->query($query,array((int) $cat_object_id,(int) $category_id));

		$query = "insert into `".BIT_DB_PREFIX."tiki_category_objects`(`cat_object_id`,`category_id`) values(?,?)";
		$result = $this->query($query,array((int) $cat_object_id,(int) $category_id));
	}

	function get_category_descendants($category_id) {
		$query = "select `category_id` from `".BIT_DB_PREFIX."tiki_categories` where `parent_id`=?";

		$result = $this->query($query,array((int) $category_id));
		$ret = array($category_id);

		while ($res = $result->fetchRow()) {
			$ret[] = $res["category_id"];

			$aux = $this->get_category_descendants($res["category_id"]);
			$ret = array_merge($ret, $aux);
		}

		$ret = array_unique($ret);
		return $ret;
	}

	function list_category_objects_deep($category_id, $offset, $maxRecords, $sort_mode = 'page_name_asc', $find) {

		$des = $this->get_category_descendants($category_id);
		if (count($des)>0) {
			$cond = "and tbl1.`category_id` in (".str_repeat("?,",count($des)-1)."?)";
		} else {
			$cond = "";
		}

		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$des = array($findesc,$findesc);
			$mid = " where (UPPER(`name`) like ? or UPPER(`description`) like ?)";
		} else {
			$mid = "";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` $cond $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select distinct tbl1.`cat_object_id` from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` $cond $mid";
		$result = $this->query($query,$des,$maxRecords,$offset);
		$result2 = $this->query($query_cant,$des);
		$cant = $result2->numRows();
		$cant2
			= $this->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` $cond $mid",$des);
		$ret = array();
		$objs = array();

		while ($res = $result->fetchRow()) {
			if (!in_array($res["cat_object_id"], $objs)) {
				$ret[] = $res;

				$objs[] = $res["cat_object_id"];
			}
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$retval["cant2"] = $cant2;
		return $retval;
	}

	function list_category_objects($category_id, $offset, $maxRecords, $sort_mode = 'page_name_asc', $find) {
		global $gBitSystem;

		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$bindvars=array((int) $category_id,$findesc,$findesc);
			$mid = " and (UPPER(tbl2.`name`) like ? or UPPER(tbl2.`description`) like ?)";
		} else {
			$mid = "";
			$bindvars=array((int) $category_id);
		}

		$query = "select tbl1.`cat_object_id`,`category_id`,`object_type`,`object_id`,`description`,
			`created`,`name`,`href`,`hits`
			from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` and tbl1.`category_id`=? $mid order by tbl2.".$this->convert_sortmode($sort_mode);
		$query_cant = "select distinct tbl1.`cat_object_id` from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` and tbl1.`category_id`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$result2 = $this->query($query_cant,$bindvars);
		$cant = $result2->numRows();
		$cant2
			= $this->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` and tbl1.`category_id`=? $mid",$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if( defined( 'BITPAGE_CONTENT_TYPE_GUID' ) && $res['object_type'] == BITPAGE_CONTENT_TYPE_GUID ) {
				$res['href'] = WIKI_PKG_URL.'index.php?content_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'trackers' ) && preg_match( '/tracker*/i', $res['object_type'] ) ) {
				$res['href'] = TRACKER_PKG_URL.'view_tracker.php?tracker_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'quizzes' ) && preg_match( '/quiz*/i', $res['object_type'] ) ) {
				$res['href'] = QUIZZES_PKG_URL.'take_quiz.php?quiz_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'articles' ) && preg_match( '/article*/i', $res['object_type'] ) ) {
				$res['href'] = ARTICLES_PKG_URL.'read.php?article_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'faqs' ) && preg_match( '/faq*/i', $res['object_type'] ) ) {
				$res['href'] = FAQS_PKG_URL.'view.php?faq_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'blogs' ) && preg_match( '/bitblogpost/i', $res['object_type'] ) ) {
				$res['href'] = BLOGS_PKG_URL.'view_post.php?content_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'blogs' ) && preg_match( '/bitblog/i', $res['object_type'] ) ) {
				$res['href'] = BLOGS_PKG_URL.'view.php?blog_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'directory' ) && preg_match( '/directory*/i', $res['object_type'] ) ) {
				$res['href'] = DIRECTORY_PKG_URL.'index.php?parent='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'imagegals' ) && preg_match( '/image*/i', $res['object_type'] ) ) {
				$res['href'] = IMAGEGALS_PKG_URL.'browse.php?gallery_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'filegals' ) && preg_match( '/file*/i', $res['object_type'] ) ) {
				$res['href'] = FILEGALS_PKG_URL.'list_file_gallery.php?gallery_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'bitforums' ) && preg_match( '/forum*/i', $res['object_type'] ) ) {
				$res['href'] = BITFORUMS_PKG_URL.'view.php?forum_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'polls' ) && preg_match( '/poll*/i', $res['object_type'] ) ) {
				$res['href'] = POLLS_PKG_URL.'form.php?poll_id='.$res['object_id'];
			} elseif( $gBitSystem->isPackageActive( 'surveys' ) && preg_match( '/survey*/i', $res['object_type'] ) ) {
				$res['href'] = SURVEYS_PKG_URL.'survey_stats_survey.php?survey_id='.$res['object_id'];
			}
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$retval["cant2"] = $cant2;

		return $retval;
	}

	function get_object_categories($type, $obj_id) {
		$ret = array();
		if ( $type and $obj_id ) {
			$query = "select tc.`category_id`,tc.`name`,tco.`cat_object_id`,tc.`parent_id` ";
			$query.= " from `".BIT_DB_PREFIX."tiki_category_objects` tco, `".BIT_DB_PREFIX."tiki_categorized_objects` tto, `".BIT_DB_PREFIX."tiki_categories` tc
        	where tco.`cat_object_id`=tto.`cat_object_id` and tco.`category_id`=tc.`category_id` and `object_type`=? and `object_id`=?";

			$bindvars=array($type,(string)$obj_id);
			if( $result = $this->query($query,$bindvars) ) {
				while ($res = $result->fetchRow()) {
					$ret[] = $res;
				}
			}
		}
		return $ret;
	}

	/*shared*/
	// \todo remove hardcoded html from get_category_path()
	function get_category_path($cats) {
		if( is_numeric( $cats ) ) {
			$cats = array( array( $cats ) );
		} elseif( !is_array( current( $cats ) ) ) {
			$cats = array( $cats );
		}
		$catpath['linked'] = '';
		$catpath['static'] = '';

		foreach ($cats as $cat) {
			$catpath['linked'] .= '<span class="categpath">';
			$path = '';
			$path['linked'] = '<a class="categpath" href="'.CATEGORIES_PKG_URL.'index.php?parent_id=' . $cat["category_id"] . '">' . $cat["name"] . '</a>';
			$path['static'] = $cat["name"];
			while( !empty( $cat["parent_id"] ) ) {
				$cat = $this->get_category( $cat["parent_id"] );
				$path['linked'] = '<a class="categpath" href="'.CATEGORIES_PKG_URL.'index.php?parent_id=' . $cat["category_id"] . '">' . $cat["name"] . '</a> &raquo; ' . $path['linked'];
				$path['static'] = $cat["name"] . ' &raquo; ' . $path['static'];
			}
			$catpath['root_category_id'] = $cat['category_id'];
			$catpath['linked'] .= $path['linked'] . '</span> , ';
			$catpath['static'] .= $path['static'].', ';
		}
		$catpath['linked'] = rtrim( $catpath['linked'], ', ' );
		$catpath['static'] = rtrim( $catpath['static'], ', ' );
		return $catpath;
	}

	/*shared*/
	// function enhancing php in_array() function
	function in_multi_array($needle, $haystack) {
		$in_multi_array = false;

		if (in_array($needle, $haystack)) {
		$in_multi_array = true;
		} else {
		while (list($tmpkey, $tmpval) = each($haystack)) {
			if (is_array($haystack[$tmpkey])) {
			if ($this->in_multi_array($needle, $haystack[$tmpkey])) {
				$in_multi_array = true;
				break;
			}
			}
		}
		}
		return $in_multi_array;
	}

	/*shared*/
	function get_categoryobjects($cats) {
		global $gBitSystem, $gBitSmarty;

		$typetitles = array();
		if( $gBitSystem->isPackageActive( 'articles' ) ) {
			require_once( ARTICLES_PKG_PATH.'BitArticle.php' );
			$typetitles[BITARTICLE_CONTENT_TYPE_GUID] = "Articles";
		}
		if( $gBitSystem->isPackageActive( 'blogs' ) ) {
			require_once( BLOGS_PKG_PATH.'BitBlog.php' );
			require_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
			$typetitles[BITBLOG_CONTENT_TYPE_GUID] = "Blogs";
			$typetitles[BITBLOGPOST_CONTENT_TYPE_GUID] = "Blog Posts";
		}
		if( $gBitSystem->isPackageActive( 'fisheye' ) ) {
			require_once( FISHEYE_PKG_PATH.'FisheyeGallery.php' );
			require_once( FISHEYE_PKG_PATH.'FisheyeImage.php' );
			$typetitles[FISHEYEGALLERY_CONTENT_TYPE_GUID] = "Image Galleries";
			$typetitles[FISHEYEIMAGE_CONTENT_TYPE_GUID] = "Images";
		}
		if( $gBitSystem->isPackageActive( 'wiki' ) ) {
			require_once( WIKI_PKG_PATH.'BitPage.php' );
			$typetitles[BITPAGE_CONTENT_TYPE_GUID] = "Wiki";
		}
/*
		// TODO: move this array to a lib
		// array for converting long type names to translatable headers (same strings as in application menu)
			"directory" => "Directories",
			"faq" => "FAQs",
			"file gallery" => "File Galleries",
			"forum" => "Forums",
			"newsletter" => "Newsletters",
			"poll" => "Polls",
			"quiz" => "Quizzes",
			"survey" => "Surveys",
			"tracker" => "Trackers",
			);
*/
		// string given back to caller
		$out = "";

		// array with items to be displayed
		$listcat = array();
		// title of categories
		$title = '';
		$find = "";
		$offset = 0;
		$maxRecords = 500;
		$count = 0;
		$sort = 'name_asc';

		foreach( $cats as $cat ) {
			// store name of category
			// \todo remove hardcoded html
			if ($count != 0) {
				$title .= '| <a href="'.CATEGORIES_PKG_URL.'index.php?parent_id='.$cat['category_id'].'">'.$cat['name'].'</a> ';
			} else {
				$title .= '<a href="'.CATEGORIES_PKG_URL.'index.php?parent_id='.$cat['category_id'] .'">'.$cat['name'].'</a> ';
			}

			// keep track of how many categories there are for split mode off
			$count++;
			$subcategs = array();
			$subcategs = $this->get_category_descendants( $cat['category_id'] );

			// array with objects in category
			$objectcat = array();
			$objectcat = $this->list_category_objects( $cat['category_id'], $offset, $maxRecords, $sort, $find);

			foreach ($objectcat["data"] as $obj) {
				$type = $obj["object_type"];
				if (!($this->in_multi_array($obj['name'], $listcat))) {
					if (isset($typetitles["$type"])) {
						$listcat["{$typetitles["$type"]}"][] = $obj;
					} elseif (isset($type)) {
						$listcat["$type"][] = $obj;
					}
				}
			}

			// split mode: appending onto $out each time
			$gBitSmarty->assign("title", $title);
			$gBitSmarty->assign("listcat", $listcat);
			$out .= $gBitSmarty->fetch("bitpackage:wiki/simple_plugin.tpl");
			// reset array for next loop
			$listcat = array();
			// reset title
			$title = '';
			$count = 0;
		}

		// non-split mode
		//	$gBitSmarty -> assign("title", $title);
		//	$gBitSmarty -> assign("listcat", $listcat);
		//	$out = $gBitSmarty -> fetch("bitpackage:wiki/simple_plugin.tpl");
		return $out;
	}

	function get_category_objects($category_id) {
		// Get all the objects in a category
		$query = "select * from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 where tbl1.`cat_object_id`=tbl2.`cat_object_id` and `category_id`=?";

		$result = $this->query($query,array((int) $category_id));
		$ret = array();

		while ($res = $result->fetchRow()) {
			if( preg_match( '/wiki*/i', $res['object_type'] ) ) {
				$res['href'] = WIKI_PKG_URL.'?page='.$res['object_id'];
			}
			$ret[] = $res;
		}

		return $ret;
	}

	function get_last_categ_objects($category_id,$type='',$newerthan=30,$num=3) {
		$mid = '';
		$bindvars = array(date('U')-($newerthan*60*60*24),$category_id);
		if ($type) {
			$mid = "and `object_type`=?";
			$bindvars[] = $type;
		}
		$sort_mode = 'created_desc';
		$query = "select tbl2.`object_id` from `".BIT_DB_PREFIX."tiki_category_objects` tbl1,`".BIT_DB_PREFIX."tiki_categorized_objects` tbl2 ";
		$query.= " where tbl1.`cat_object_id`=tbl2.`cat_object_id` and created > ? and `category_id`=? $mid order by tbl2.".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars);
		$rs = $result->GetArray();
		$ret = array();
		if (count($rs)) {
			if (count($rs) < $num) {
				$num = count($rs);
			}
			$found = array_rand($rs, $num);
			if (is_array($found)) {
				foreach ($found as $f) {
					$ret[] = $rs[$f]['obj_id'];
				}
			} else {
				$ret[] = $rs[$found]['obj_id'];
			}
		}
		return $ret;
	}

	function uncategorize_object($type, $id) {
		if( $this->verifyId( $id ) ) {
			// Fixed query. -rlpowell
			$query = "select `cat_object_id`  from `".BIT_DB_PREFIX."tiki_categorized_objects` where `object_type`=? and `object_id`=?";
			$cat_object_id = $this->getOne($query, array($type,$id));

			if ($cat_object_id) {
				$query = "delete from `".BIT_DB_PREFIX."tiki_category_objects` where `cat_object_id`=?";
				$result = $this->query($query,array((int) $cat_object_id));
				$query = "delete from `".BIT_DB_PREFIX."tiki_categorized_objects` where `cat_object_id`=?";
				$result = $this->query($query,array((int) $cat_object_id));
			}
		}
	}

	function uncategorize($type, $obj_id) {
		$categs = $this->get_object_categories($type, $obj_id);
		foreach ($categs as $cat) {
			$this->remove_object_from_category($cat['cat_object_id'],$cat['category_id']);
		}
	}

	function remove_object_from_category($cat_object_id, $category_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_category_objects` where `cat_object_id`=? and `category_id`=?";

		$result = $this->query($query,array($cat_object_id,$category_id));
		// If the object is not listed in any category then remove the object
		$query = "select count(*) from `".BIT_DB_PREFIX."tiki_category_objects` where `cat_object_id`=?";
		$cant = $this->getOne($query,array((int) $cat_object_id));

		if (!$cant) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_categorized_objects` where `cat_object_id`=?";

			$result = $this->query($query,array((int) $cat_object_id));
		}
	}

	// FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS ////
	function categorize_page( $pContentId, $categId ) {
		require_once( WIKI_PKG_PATH.'BitPage.php' );
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized( BITPAGE_CONTENT_TYPE_GUID, $pContentId );

		if (!$cat_object_id) {
			// The page is not cateorized
			$catPage = new BitPage( NULL, $pContentId );
			if( $catPage->load() ) {
				$cat_object_id = $this->add_categorized_object( BITPAGE_CONTENT_TYPE_GUID, $pContentId, substr($catPage->mInfo["description"], 0, 200), substr($catPage->mInfo["title"], 0, 200), $catPage->getDisplayUrl() );
			}
		}

		$this->categorize($cat_object_id, $categId);
	}

	function categorize_tracker($tracker_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page

		$cat_object_id = $this->is_categorized('tracker', $tracker_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_tracker($tracker_id);

			$href = TRACKERS_PKG_URL.'view_tracker.php?tracker_id=' . $tracker_id;
			$cat_object_id = $this->add_categorized_object('tracker', $tracker_id, substr($info["description"], 0, 200),$info["name"] , $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_quiz($quiz_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('quiz', $quiz_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_quiz($quiz_id);

			$href = QUIZZES_PKG_URL.'take_quiz.php?quiz_id=' . $quiz_id;
			$cat_object_id
				= $this->add_categorized_object('quiz', $quiz_id, substr($info["description"], 0, 200), $info["name"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_article($article_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('article', $article_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_article($article_id);

			$href = ARTICLES_PKG_URL.'read.php?article_id=' . $article_id;
			$cat_object_id = $this->add_categorized_object('article', $article_id, $info["heading"], $info["title"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_faq($faq_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('faq', $faq_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_faq($faq_id);

			$href = FAQS_PKG_URL.'view.php?faq_id=' . $faq_id;
			$cat_object_id = $this->add_categorized_object('faq', $faq_id, $info["description"], $info["title"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_blog($blog_id, $category_id) {
		require_once( BLOGS_PKG_PATH.'BitBlog.php' );
		global $gBlog;
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('blog', $blog_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $gBlog->get_blog($blog_id);

			$href = BLOGS_PKG_URL.'view.php?blog_id=' . $blog_id;
			$cat_object_id = $this->add_categorized_object('blog', $blog_id, $info["description"], $info["title"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_blog_post($post_id, $category_id, $purge=false) {
		global $gBlog;
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('blogpost', $post_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$blogPost = new BitBlogPost( $post_id );
			if( $blogPost->load() ) {
				$href = BLOGS_PKG_URL.'view_post.php?post_id=' . $post_id;
				$cat_object_id = $this->add_categorized_object('blogpost', $post_id, $blogPost->mInfo["user"], $blogPost->mInfo["title"], $href);
			}
		} elseif ($purge) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_category_objects` where `cat_object_id`=? and `category_id`=?";
			$this->query($query,array($cat_object_id,$category_id));
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_directory($directory_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('directory', $directory_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_directory($directory_id);
			if (empty($info['title'])) {
				$info['title'] = "post in ".$info['real_name']." blog";
			}
			$href = DIRECTORY_PKG_URL.'index.php?parent=' . $directory_id;
			$catObject_id = $this->add_categorized_object('directory', $directory_id, $info["description"], $info["name"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_gallery($gallery_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('image gallery', $gallery_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_gallery($gallery_id);

			$href = IMAGEGALS_PKG_URL.'browse.php?gallery_id=' . $gallery_id;
			$cat_object_id = $this->add_categorized_object('image gallery', $gallery_id, $info["description"], $info["name"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_file_gallery($gallery_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('file gallery', $gallery_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_file_gallery($gallery_id);

			$href = FILEGALS_PKG_URL.'list_file_gallery.php?gallery_id=' . $gallery_id;
			$cat_object_id = $this->add_categorized_object('file gallery', $gallery_id, $info["description"], $info["name"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_forum($forum_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('forum', $forum_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_forum($forum_id);

			$href = BITFORUMS_PKG_URL.'view.php?forum_id=' . $forum_id;
			$cat_object_id = $this->add_categorized_object('forum', $forum_id, $info["description"], $info["name"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}

	function categorize_poll($poll_id, $category_id) {
		// Check if we already have this object in the tiki_categorized_objects page
		$cat_object_id = $this->is_categorized('poll', $poll_id);

		if (!$cat_object_id) {
			// The page is not cateorized
			$info = $this->get_poll($poll_id);

			$href = POLLS_PKG_URL.'form.php?poll_id=' . $poll_id;
			$cat_object_id = $this->add_categorized_object('poll', $poll_id, $info["title"], $info["title"], $href);
		}

		$this->categorize($cat_object_id, $category_id);
	}
	// FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS END ////
	function get_child_categories($category_id) {
		$ret = array();

		$query = "select * from `".BIT_DB_PREFIX."tiki_categories` where `parent_id`=?";
		$result = $this->query($query,array($category_id));

		while ($res = $result->fetchRow()) {
			$id = $res["category_id"];

			$query = "select count(*) from `".BIT_DB_PREFIX."tiki_categories` where `parent_id`=?";
			$res["children"] = $this->getOne($query,array($id));
			$query = "select count(*) from `".BIT_DB_PREFIX."tiki_category_objects` where `category_id`=?";
			$res["objects"] = $this->getOne($query,array($id));
			$ret[] = $res;
		}

		return $ret;
	}

	function get_all_categories() {
		$query = " select `name`,`category_id`,`parent_id` from `".BIT_DB_PREFIX."tiki_categories` order by `name`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	// Same as get_all_categories + it also get info about count of objects
	function get_all_categories_ext() {
		$ret = array();

		$query = "SELECT tc.`category_id`, COUNT(`cat_object_id`) AS `objects`,`name`,`parent_id`,`description`,`hits` 
				  FROM `".BIT_DB_PREFIX."tiki_categories` tc LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_category_objects` tco ON(tc.category_id=tco.category_id) 
				  GROUP BY tc.`category_id`,`parent_id`,`name`,`description`,`hits` order by `name`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$id = $res["category_id"];

//			$query = "select count(*) from `".BIT_DB_PREFIX."tiki_categories` where `parent_id`=?";
//			$res["children"] = $this->getOne($query,array($id));
//			$query = "select count(*) from `".BIT_DB_PREFIX."tiki_category_objects` where `category_id`=?";
//			$res["objects"] = $this->getOne($query,array($id));
			$ret[] = $res;
		}
		return $ret;
	}

	// get categories related to a link. For Whats related module.
	function get_link_categories($link) {
		$ret=array();
		$parsed=parse_url($link);
		$parsed["path"]=end(split("/",$parsed["path"]));
		if(!isset($parsed["query"])) return($ret);
		/* not yet used. will be used to get the "base href" of a page
		$params=array();
		$a = explode('&', $parsed["query"]);
		for ($i=0; $i < count($a);$i++) {
			$b = split('=', $a[$i]);
			$params[htmlspecialchars(urldecode($b[0]))]=htmlspecialchars(urldecode($b[1]));
		}
		*/
		$query="select distinct co.`category_id` from `".BIT_DB_PREFIX."tiki_categorized_objects` cdo, `".BIT_DB_PREFIX."tiki_category_objects` co  where cdo.`href`=? and cdo.`cat_object_id`=co.`cat_object_id`";
		$result=$this->query($query,array($parsed["path"]."?".$parsed["query"]));
		while ($res = $result->fetchRow()) {
		  $ret[]=$res["category_id"];
		}
		return($ret);
	}

	// input is a array of category id's and return is a array of
	// max_rows related links with description
	function get_related($categories,$max_rows=10) {
		if(count($categories)==0) return (array());
		$quarr=implode(",",array_fill(0,count($categories),'?'));
		$query="select distinct cdo.`object_type`, cdo.`description`, cdo.`object_id`,cdo.`href` from `".BIT_DB_PREFIX."tiki_categorized_objects` cdo, `".BIT_DB_PREFIX."tiki_category_objects` co  where co.`category_id` in (".$quarr.") and co.`cat_object_id`=cdo.`cat_object_id`";
		$result=$this->query($query,$categories);
		$ret=array();
		while ($res = $result->fetchRow()) {
			if (empty($res["description"])) {
				$ret[$res["href"]]=$res["type"].": ".$res["obj_id"];
			} else {
				$ret[$res["href"]]=$res["type"].": ".$res["description"];
			}
		}
		if (count($ret)>$max_rows) {
			$ret2=array();
			$rand_keys = array_rand ($ret,$max_rows);
			foreach($rand_keys as $value) {
				$ret2[$value]=$ret[$value];
			}
			return($ret2);
		}
		return($ret);
	}

	// combines the two functions above
	function get_link_related($link,$max_rows=10) {
		return ($this->get_related($this->get_link_categories($link),$max_rows));
	}

}

global $categlib;
$categlib = new CategLib();

?>
