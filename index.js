/**
 * Run Coverage Tests / Load Module
 *
 * @author potanin@UD
 */
module.exports = process.env.APP_COVERAGE ? require( './static/codex/lib-cov/models' ) : require( './scripts/models' );
