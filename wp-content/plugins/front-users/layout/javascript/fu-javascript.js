function fu_comments(args) {
	$.ajax({
		url: '/wp-content/plugins/front-users/front-users.php',
		dataType: json,
		data: ( {id: args['comment_id'], voted: args['vote'], rating: args['comment_rating']} ),
		type: 'POST'
	});
}

id_add_action( 'comment_vote', fu_comments);
