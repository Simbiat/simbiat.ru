/**
 * @file Build ESLint and Stylelint plugins.
 */
import { $ } from 'bun';

const plugins: string[] = [
    'build/js/dev/ESLintPlugin',
    'build/js/dev/HTMLESLintPlugin',
    'build/js/dev/StylelintPlugin',
];

for (const plugin of plugins) {
    $.cwd(plugin);
    try {
        // eslint-disable-next-line no-await-in-loop
        await $`bun run prepare`;
        // eslint-disable-next-line no-console
        console.log(`✅ ${plugin}: OK`);
    } catch (error) {
        const code = (error as { exitCode?: number }).exitCode ?? 1;
        // eslint-disable-next-line no-console
        console.error(`${plugin}: prepare failed with exit code ${code}`);
        process.exit(code);
    }
}
